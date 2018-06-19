<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Variety;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Form\Component\VarietyType;
use AppBundle\Service\Precifier\ComponentsListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\Component\ComponentFileHandler;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * Variety controller.
 *
 * @Route("twig/variety")
 * @Breadcrumb("Variedades")
 *
 */
class VarietyController extends AbstractController
{
    /**
     * @Route("/", name="variety_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('variety');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('v')
            ->from(Variety::class, 'v')
            ->leftJoin('v.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('v.description', 'asc');

        if ($components_actives = $request->get('actives')) {
            if ((int) $components_actives == 1) {
                $expression  =
                    $qb->expr()->neq(
                        'v.generatorLevels',
                        $qb->expr()->literal('[]'));
            } else {
                $expression  =
                    $qb->expr()->eq(
                        'v.generatorLevels',
                        $qb->expr()->literal('[]'));
            }

            $qb->andWhere(
                $expression
            );
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10
        );

        return $this->render('Variety.index', array(
            'pagination' => $pagination,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all()),
            'components_active_val' => $components_actives
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/create", name="create_variety")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('variety');
        $variety = $manager->create();
        $form = $this->createForm(VarietyType::class, $variety);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($variety);

            $this->get('component_file_handler')->upload($variety, $request->files);

            $manager->save($variety);

            $this->setNotice('Variedade cadastrada com sucesso. ');

            /** @var ComponentsListener $componentListener */
            $componentListener = $this->container->get('precifier_components_listener');

            $componentListener->action(Memorial::ACTION_TYPE_ADD_COMPONENT, ComponentInterface::FAMILY_VARIETY);

            return $this->redirectToRoute('variety_index');
        }

        return $this->render('Variety.form', array(
            'variety' => $variety,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="variety_show")
     * @Method("GET")
     */
    public function showAction(Variety $variety)
    {
        $deleteForm = $this->createDeleteForm($variety);

        return $this->render('Variety.show_content', array(
            'variety' => $variety,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     *
     * @Route("/{id}/update", name="variety_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Variety $variety)
    {
        $deleteForm = $this->createDeleteForm($variety);
        $editForm = $this->createForm('AppBundle\Form\Component\VarietyType', $variety);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('component_file_handler')->upload($variety, $request->files);

            $this->manager('variety')->save($variety);

            $message = 'Variedade atualizada com sucesso. ';

            $this->setNotice($message);

            return $this->redirectToRoute('variety_index');
        }

        return $this->render('Variety.form', array(
            'variety' => $variety,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/{id}/delete/", name="variety_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Variety $variety)
    {
        $usageManager = $this->manager('Project_Variety');

        if ($usageManager->findOneBy(['variety' => $variety->getId()])) {
            $message = 'Esta variedade não pode ser excluída';
            $status = Response::HTTP_LOCKED;
        } else {
            try {
                $message = 'Variedade excluída com sucesso';
                $status = Response::HTTP_OK;
                $this->manager('variety')->delete($variety);

                /** @var ComponentsListener $componentListener */
                $componentListener = $this->container->get('precifier_components_listener');

                $componentListener->action(Memorial::ACTION_TYPE_REMOVE_COMPONENT, ComponentInterface::FAMILY_VARIETY);

            } catch (\Exception $exception) {
                $message = 'Falha ao excluir variedade';
                $status = Response::HTTP_CONFLICT;
            }
        }

        return $this->json([
            'message' => $message
        ], $status);
    }

    /**
     * @param Variety $variety The variety entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Variety $variety)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('variety_delete', array('id' => $variety->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
