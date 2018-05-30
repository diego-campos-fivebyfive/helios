<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Kit;
use AppBundle\Form\Kit\KitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
USE Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("kit")
 *
 * @Breadcrumb("Kits Fixos")
 */
class KitsController extends AbstractController
{
    /**
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

            $components = $request->get('components') ?? [];

            $kit->setComponents($components);

            $manager->save($kit);

            $this->get('component_file_handler')->upload($kit, $request->files);

            $manager->save($kit);

            $this->setNotice('Kit cadastrado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        return $this->render("kit/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/{id}/update", name="update_kit")
     */
    public function updateAction(Request $request, Kit $kit)
    {
        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->manager('kit');

            // TODO: revisar salvamento de imagem
            $this->get('component_file_handler')->upload($kit, $request->files);

            $components = $request->get('components') ?? [];

            $kit->setComponents($components);

            $manager->save($kit);

            $this->setNotice('Kit cadastrado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        return $this->render("kit/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/{id}", name="kit_show")
     * @Method("GET")
     */
    public function showAction(Kit $kit)
    {
        return $this->render('kit/show.html.twig', array(
            'kit' => $kit
        ));
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
        try {
            $this->manager('kit')->delete($kit);
            $message = 'Kit excluído com sucesso';
            $status = Response::HTTP_OK;
        } catch (\Exception $exception) {
            $message = 'Falha ao excluir este Kit';
            $status = Response::HTTP_CONFLICT;
        }

        return $this->json(['message' => $message], $status);
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
