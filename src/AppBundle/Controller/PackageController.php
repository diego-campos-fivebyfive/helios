<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use AppBundle\Entity\Package;
use AppBundle\Form\PackageType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Packages", route={"name"="package_index"})
 *
 * @Route("package")
 * @Security("has_role('ROLE_ADMIN')")
 */
class PackageController extends AbstractController
{
    /**
     * @Route("/", name="package_index")
     */
    public function indexAction(Request $request)
    {
        //$paginator = $this->get('knp_paginator');

        $manager = $this->getPackageManager();

        /*$query = $manager
                ->getEntityManager()
                ->createQueryBuilder()
                ->select('p')
                ->from('AppBundle:Package', 'p')
                ->getQuery();
        //TODO SCOPE

        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 4
        );*/

        $packages = $manager->findAll();

        return $this->render('package.index', array(
            'packages' => $packages
        ));
    }

    /**
     * @Breadcrumb("Create Package", route={"name"="package_create"})
     *
     * @Route("/create", name="package_create")
     * @Method({"get", "post"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->getPackageManager();
        $package = $manager->create();
        $form = $this->createForm(PackageType::class, $package);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            //TODO validation

            $manager->save($package);

            $this->setNotice('Plano cadastrado com sucesso!');

            return $this->redirectToRoute('package_index');
        }
        return $this->render('package.form', array(
                    'package' => $package,
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/update/{id}", name="package_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request, Package $package)
    {
        $editForm = $this->createForm('AppBundle\Form\PackageType', $package);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getPackageManager()->save($package);

            $this->setNotice('Plano atualizado com sucesso!');

            return $this->redirectToRoute('package_index');
        }

        return $this->render('package.form', array(
                    'package' => $package,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="package_delete")
     */
    public function deleteAction(Request $request, Package $package)
    {

        $accounts = $package->getAccounts();

        // TODO VERIFY
        if (count($accounts) > 0)
        {
            return $this->redirectToRoute('package_index');
        }

        $this->getPackageManager()->delete($package);

        return $this->redirectToRoute('package_index');
    }

}
