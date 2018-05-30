<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Kit;
use AppBundle\Form\Kit\KitType;
use Doctrine\Common\Util\Inflector;
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
            'structure' => $kit
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
}
