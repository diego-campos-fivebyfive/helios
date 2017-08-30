<?php

namespace AppBundle\Controller\Platform;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\ComponentTrait;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Form\Extra\MemorialFilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @Route("/memorials")
 */
class MemorialsController extends AbstractController
{
    /**
     * @Route("/", name="platform_memorials_index")
     */
    public function indexAction(Request $request)
    {
        $memorials = $this->manager('memorial')->findBy([
            'status' => true
        ]);

        return $this->render('platform/memorials/index.html.twig', [
            'memorials' => $memorials,
        ]);
    }

    /**
     * @Route("/products", name="platform_products")
     */
    public function getProductsAction(Request $request)
    {
        $form = $this->createForm(MemorialFilterType::class, null, [
            'manager' => $this->manager('memorial')
        ]);

        $form->handleRequest($request);

        $products = null;
        $data = $form->getData();
        if($form->isSubmitted() && $form->isValid() && $data['level']){

            $products = $this->renderRanges($data['memorial'], $data['level'])->getContent();
        }

        return $this->render('platform/memorials/filter.html.twig', [
            'form' => $form->createView(),
            'products' => $products
        ]);
    }

    /**
     * @Route("/ranges/save", name="platform_range_save")
     */
    public function saveRangeAction(Request $request)
    {
        $data = $request->isMethod('post') ? $request->request->all() : $request->query->all() ;

        $id = (int) $data['id'];
        $manager = $this->manager('range');

        /** @var Range $range */
        $range = $id ? $manager->find($id) : $manager->create() ;
        unset($data['id']);

        $data['memorial'] = $this->manager('memorial')->find($data['memorial']);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $property => $value){
            $accessor->setValue($range, $property, $value);
        }

        $manager->save($range);

        return $this->json([
            'range' => $range->toArray()
        ]);
    }

    /**
     * @Route("/", name="platform_memorials")
     */
    public function getMemorialsAction()
    {
        $memorials = $this->manager('memorial')->findBy([]);

        return $this->render('platform/memorials/memorials.html.twig', [
            'memorials' => $memorials
        ]);
    }

    /**
     * @param Memorial $memorial
     * @param $level
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderRanges(Memorial $memorial, $level)
    {
        $products = $this->manager('module')->findAll();
        $rangeManager = $this->manager('range');

        $data = [];
        /** @var ComponentTrait|ModuleInterface $product */
        foreach ($products as $product){

            $ranges = $rangeManager->findBy([
                'code' => $product->getCode(),
                'memorial' => $memorial,
                'level' => $level
            ]);

            /** @var Range $range */
            foreach($ranges as $range){

                $key = sprintf('%s-%s', (int)$range->getInitialPower(), (int)$range->getFinalPower());

                if (!array_key_exists($key, $data)) {
                    $data['columns'][$key] = $range;
                }

                $data['ranges'][$product->getCode()][$key] = $range;
            }

            $data['products'][$product->getCode()] = $product;
        }

        return $this->render('platform/memorials/ranges.html.twig', [
            'memorial' => $memorial,
            'data' => $data,
        ]);
    }
}