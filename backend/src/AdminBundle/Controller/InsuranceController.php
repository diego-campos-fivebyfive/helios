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
 * @Route("twig/insurance")
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

            $this->formatValues($insurance, true);

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
        $this->formatValues($insurance);

        $form = $this->createForm(InsuranceType::class, $insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->formatValues($insurance, true);

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
     * @param Additive $insurance
     * @param bool $toDb
     */
    private function formatValues(Additive $insurance, $toDb = false)
    {
        $properties = [
            'Value',
            'MinPower',
            'MaxPower',
            'MinPrice',
            'MaxPrice'
        ];

        foreach ($properties as $property){
            $getValue = 'get'.$property;
            $setValue = 'set'.$property;

            if ($insurance->$getValue()){
                if ($toDb)
                    $insurance->$setValue(str_replace(',','.', $insurance->$getValue()));
                else
                    $insurance->$setValue(str_replace('.',',', $insurance->$getValue()));
            }

        }
    }
}
