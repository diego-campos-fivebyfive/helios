<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Component\Maker;
use AppBundle\Form\Component\MakerType;

/**
 * MakerController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("maker")
 *
 * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
 *
 * @Breadcrumb("Makers", route={"name"="maker_index"})
 */
class MakerController extends AbstractController
{
    /**
     * @Route("/", name="maker_index")
     *
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('maker');
        $paginator = $this->getPaginator();

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('m')->from(Maker::class, 'm')->orderBy('m.name');

        $currentFamily = null;
        if ($currentFamily = $request->get('family')) {
            $qb->andWhere('m.context = :context');
            $qb->setParameter('context', $currentFamily);
        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(), $request->query->getInt('page', 1), 10
        );

        $families = [
            ComponentInterface::FAMILY_MODULE => ComponentInterface::FAMILY_MODULE,
            ComponentInterface::FAMILY_INVERTER => ComponentInterface::FAMILY_INVERTER,
            ComponentInterface::FAMILY_STRING_BOX => ComponentInterface::FAMILY_STRING_BOX,
            ComponentInterface::FAMILY_STRUCTURE => ComponentInterface::FAMILY_STRUCTURE,
            ComponentInterface::FAMILY_VARIETY => ComponentInterface::FAMILY_VARIETY
        ];

        return $this->render("maker.index", [
            'pagination' => $pagination,
            'families' => $families,
            'current_family' => $currentFamily
        ]);
    }

    /**
     * @Breadcrumb("Add")
     * @Route("/create", name="maker_create")
     * @Method({"get","post"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('maker');
        $maker = $manager->create();

        $form = $this->createForm(MakerType::class, $maker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($maker);
            $this->setNotice("Fabricante cadastrado com sucesso !");
            return $this->redirectToRoute('maker_index');
        }

        return $this->render("maker.form", [
            'form' => $form->createView(),
            'maker' => $maker
        ]);
    }

    /**
     * @Breadcrumb("Edit")
     * @Route("/update/{id}", name="maker_update")
     * @Method({"get","post"})
     */
    public function updateAction(Request $request, Maker $maker)
    {
        $manager = $this->manager('maker');
        $form = $this->createForm(MakerType::class, $maker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($maker);
            $this->setNotice("Fabricante Atualizado com sucesso !");
            return $this->redirectToRoute('maker_index');
        }

        return $this->render("maker.form", [
            'form' => $form->createView(),
            'maker' => $maker
        ]);
    }

    /**
     * @Route("/delete/{id}", name="maker_delete")
     * @Method("get")
     */
    public function deleteAction(Request $request, Maker $maker)
    {
        $manager = $this->manager('maker');
        if (count($maker->getInverters()) > 0 || count($maker->getModules()) > 0) {
            $this->setNotice("-Este fabricante possui um ou mais produtos<br>-Remova os produtos antes de efetuar a remoção do fabricante", "error");
            return $this->redirectToRoute("maker_index");
        }
        $manager->delete($maker);
        $this->setNotice("Fabricante removido com sucesso !");
        return $this->redirectToRoute("maker_index");
    }

    /**
     * @Route("/fast-create", name="maker_fast_create")
     */
    public function fastCreateAction(Request $request)
    {
        $account = $this->account();
        $name = $request->get('name');

        $context = 'module_maker' == $request->get('source', 'inverter_maker')
            ? MakerInterface::CONTEXT_MODULE
            : MakerInterface::CONTEXT_INVERTER;

        $manager = $this->manager('maker');

        // Find global or account referenced
        $maker = $manager->findByUse($name, $context, $account);

        if(!$maker){

            /** @var MakerInterface $maker */
            $maker = $manager->create();

            $maker
                ->setName($name)
                ->setAccount($account)
                ->setContext($context)
                ->setEnabled(true);

            $manager->save($maker);
        }

        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'id' => $maker->getId(),
                'name' => $maker->getName()
            ]
        ]);
    }

}
