<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Misc\AdditiveInterface;
use AdminBundle\Form\Misc\InsuranceType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Breadcrumb("Seguros")
 *
 * @Route("/insurance")
 */
class InsuranceController extends AbstractController
{
    /**
     * @Route("/", name="insurance_index")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('additive');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('i')
            ->from(Additive::class, 'i')
            ->orderBy('i.id', 'desc');

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10);

        return $this->render('admin/insurance/index.html.twig', [
            'insurances' => $pagination
        ]);
    }

    /**
     * @Route("/create", name="insurance_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('additive');

        /** @var AdditiveInterface $insurance */
        $insurance = $manager->create();

        $form = $this->createForm(InsuranceType::class, $insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setReplace($insurance);
            $insurance->setType(AdditiveInterface::TYPE_INSURANCE);

            $manager->save($insurance);

            return $this->json([]);
        }

        return $this->render('admin/insurance/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="insurance_update")
     */
    public function updateAction(Request $request, Additive $insurance)
    {
        $this->getReplace($insurance);

        $form = $this->createForm(InsuranceType::class, $insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setReplace($insurance);

            if (!$insurance->getType())
                $insurance->setType(AdditiveInterface::TYPE_INSURANCE);

            $this->manager('additive')->save($insurance);

            return $this->json([]);
        }

        return $this->render('admin/insurance/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="insurance_delete")
     * @Method("delete")
     */
    public function deleteAction(Additive $insurance)
    {
        $this->manager('additive')->delete($insurance);

        return $this->json([]);
    }

    /**
     * @param $value
     * @return mixed
     */
    private function replaceValues($value)
    {
        return str_replace(',','.', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    private function reverseReplaceValues($value)
    {
        return str_replace('.',',', $value);
    }

    /**
     * @param Additive $insurance
     */
    private function getReplace(Additive $insurance)
    {
        $insurance->setValue($this->reverseReplaceValues($insurance->getValue()));
        $insurance->setMinPower($this->reverseReplaceValues($insurance->getMinPower()));
        $insurance->setMaxPower($this->reverseReplaceValues($insurance->getMaxPower()));
        $insurance->setMinPrice($this->reverseReplaceValues($insurance->getMinPrice()));
        $insurance->setMaxPrice($this->reverseReplaceValues($insurance->getMaxPrice()));
    }

    /**
     * @param Additive $insurance
     */
    private function setReplace(Additive $insurance)
    {
        $insurance->setValue($this->replaceValues($insurance->getValue()));
        $insurance->setMinPower($this->replaceValues($insurance->getMinPower()));
        $insurance->setMaxPower($this->replaceValues($insurance->getMaxPower()));
        $insurance->setMinPrice($this->replaceValues($insurance->getMinPrice()));
        $insurance->setMaxPrice($this->replaceValues($insurance->getMaxPrice()));
    }
}
