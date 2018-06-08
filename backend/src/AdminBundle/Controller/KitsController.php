<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\Kit;
use AppBundle\Form\Kit\KitType;
use AppBundle\Manager\CartHasKitManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("kit")
 *
 * @Breadcrumb("Kits Fixos")
 */
class KitsController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Route("/", name="kits_index")
     * @Method("get")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('kit');

        $qb = $manager->createQueryBuilder();

        $qb->orderBy('k.position', 'asc');

        if ($actives = $request->get('actives')) {
            if ((int) $actives == 1) {
                $expression  =
                    $qb->expr()->eq(
                        'k.available',
                        $qb->expr()->literal(1));
            } else {
                $expression  =
                    $qb->expr()->eq(
                        'k.available',
                        $qb->expr()->literal(0));
            }

            $qb->andWhere(
                $expression
            );
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('kit/index.html.twig', array(
            'pagination' => $pagination,
            'kits_active_val' => $actives
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Route("/create", name="create_kit")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('kit');

        /** @var Kit $kit */
        $kit = $manager->create();

        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->formatValues($kit, true);

            $components = $form->get('components')->getData() ?? [];

            $this->insertKitComponents($kit, $components);

            $manager->save($kit);

            $this->get('component_file_handler')->upload($kit, $request->files);

            $manager->save($kit);

            $this->setNotice('Kit cadastrado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        return $this->render("kit/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $this->getComponentFamilies()
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Route("/{id}/update", name="update_kit")
     */
    public function updateAction(Request $request, Kit $kit)
    {
        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->formatValues($kit, true);

            $manager = $this->manager('kit');

            $components = $form->get('components')->getData() ?? [];

            $this->insertKitComponents($kit, $components);

            // TODO: revisar salvamento de imagem
            $this->get('component_file_handler')->upload($kit, $request->files);

            $manager->save($kit);

            $this->setNotice('Kit atualizado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        return $this->render("kit/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $this->getComponentFamilies()
        ]);
    }

    /**
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     *
     * @Route("/{id}/delete/", name="delete_kit")
     * @Method("delete")
     */
    public function deleteAction(Kit $kit)
    {
        /** @var CartHasKitManager $cartHasKitManager */
        $cartHasKitManager = $this->container->get('cart_has_kit_manager');

        $cartKits = $cartHasKitManager->findBy([
            'kit' => $kit
        ]);

        /** @var CartHasKit $cart */
        foreach ($cartKits as $cartKit) {
            $cartHasKitManager->delete($cartKit, false);
        }

        $cartHasKitManager->flush();

        $this->manager('kit')->delete($kit);

        return $this->json(['message' => 'Kit excluído com sucesso']);
    }

    /**
     * @Route("/components/{family}", name="kit_components_by_family")
     * @Method("get")
     */
    public function getKitComponentsByFamilyAction($family)
    {
        if (!in_array($family, $this->getComponentFamilies())) {
            return JsonResponse::create([], 404);
        }

        $manager = $this->container->get("{$family}_manager");

        $field = in_array($family, ['module',  'inverter']) ? 'model' : 'description';

        /** @var QueryBuilder $qb */
        $qb = $manager->createQueryBuilder();
        $alias = $qb->getRootAlias();

        $qb->select("{$alias}.id, {$alias}.code, {$alias}.{$field} as description");

        $results = $qb->getQuery()->getResult();

        return JsonResponse::create($results, 200);
    }

    /**
     * @param Kit $kit
     * @param $components
     */
    private function insertKitComponents(Kit $kit, $components)
    {
        $componentsDecoded = json_decode($components, true);
        $oldComponents = $kit->getComponents();

        $kit->setComponents([]);

        foreach ($componentsDecoded as $component) {
            $tag = $component['family'] . '_' . $component['componentId'];
            $quantity = $component['quantity'];
            $position = $component['position'];

            if (!is_numeric($quantity)) {
                $quantity = $oldComponents[$tag]['quantity'];
            }
            if (!is_numeric($quantity)) {
                $position = $oldComponents[$tag]['position'];
            }

            $component['quantity'] = intval($quantity);
            $component['position'] = intval($position);

            if (is_numeric($quantity) && is_numeric($position)) {
                $kit->addComponent($tag, $component);
            }
        }
    }

    /**
     * @param Kit $kit
     * @param bool $toDb
     */
    private function formatValues(Kit $kit, $toDb = false)
    {
        $properties = [
            'Price',
            'Power'
        ];

        foreach ($properties as $property){
            $getValue = 'get'.$property;
            $setValue = 'set'.$property;

            if ($kit->$getValue()){
                if ($toDb) {
                    $kit->$setValue(str_replace(',', '.', $kit->$getValue()));
                }
                else {
                    $kit->$setValue(str_replace('.', ',', $kit->$getValue()));
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getComponentFamilies()
    {
        return $families = [
            ComponentInterface::FAMILY_MODULE => ComponentInterface::FAMILY_MODULE,
            ComponentInterface::FAMILY_INVERTER => ComponentInterface::FAMILY_INVERTER,
            ComponentInterface::FAMILY_STRING_BOX => ComponentInterface::FAMILY_STRING_BOX,
            ComponentInterface::FAMILY_STRUCTURE => ComponentInterface::FAMILY_STRUCTURE,
            ComponentInterface::FAMILY_VARIETY => ComponentInterface::FAMILY_VARIETY
        ];
    }
}
