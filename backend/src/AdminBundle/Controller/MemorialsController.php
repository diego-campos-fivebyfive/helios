<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Pricing\Range;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Form\Admin\MemorialFilterType;
use AppBundle\Service\Pricing\MemorialAnalyzer;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("memorials")
 */
class MemorialsController extends AdminController
{
    /**
     * @Route("/", name="memorials")
     */
    public function indexAction(Request $request)
    {
        $memorials = $this->manager('memorial')->findAll();

        return $this->render('admin/memorials/index.html.twig', [
            'memorials' => $memorials
        ]);
    }

    /**
     * @Route("/{id}/config", name="memorials_config")
     */
    public function configAction(Memorial $memorial, Request $request)
    {
        $rangeLevels = $this->manager('range')->distinct('level', ['memorial' => $memorial]);
        $accountLevels = $this->manager('account')->distinct('level');

        $levels = array_unique(array_merge($rangeLevels, $accountLevels));

        $form = $this->createForm(MemorialFilterType::class, ['memorial' => $memorial], [
            'memorial' => $memorial,
            'levels' => $levels
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $analyzer = new MemorialAnalyzer($this->container);

            $config = $form->getData();

            $collection = $analyzer->analyze($config);

            return $this->render('admin/memorials/ranges.html.twig', [
                'collection' => $collection,
                'memorial' => $memorial
            ]);
        }

        return $this->render('admin/memorials/config.html.twig', [
            'memorial' => $memorial,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/update", name="memorials_product_update")
     */
    public function updateProductAction(Request $request)
    {
        $id = $request->request->get('id');
        $field = $request->request->get('field');
        $value = $request->request->get('value');
        $type = $request->request->get('type');

        $manager = $this->manager($type);
        $component = $manager->find($id);

        if($component){

            $accessor = PropertyAccess::createPropertyAccessor();

            $accessor->setValue($component, $field, $value);

            $manager->save($component);
        }

        return $this->json([
            'product' => [
                'type' => $type,
                'id' => $id,
                'field' => $field,
                'value' => $value
            ]
        ]);
    }

    /**
     * @Route("/ranges/update", name="memorials_range_update")
     */
    public function updateRangesAction(Request $request)
    {
        $manager = $this->manager('range');
        $data = $request->request->get('ranges');

        foreach ($data as $key => $config){

            $range = $manager->find($config['id']);

            if($range instanceof Range){

                $range
                    ->setCostPrice($config['cost'])
                    ->setMarkup($config['markup'])
                ;

                $manager->save($range, false);

                $data[$key]['price'] = $range->getPrice();
            }
        }

        $manager->getEntityManager()->flush();

        return $this->json([
            'ranges' => $data
        ]);
    }
}
