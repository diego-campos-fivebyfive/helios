<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Pricing\MemorialType;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Form\Admin\MemorialFilterType;
use AppBundle\Service\Pricing\MemorialAnalyzer;
use AppBundle\Service\Pricing\MemorialCloner;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 * @Route("memorials")
 */
class MemorialController extends AdminController
{
    /**
     * @Route("/", name="memorials")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->manager('memorial')->createQueryBuilder();

        $qb->orderBy('m.createdAt', 'desc');

        $paginator = $this->getPaginator();

        $memorials = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/memorials/index.html.twig', [
            'memorials' => $memorials
        ]);
    }

    /**
     * @Route("/create", name="memorials_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('memorial');

        /** @var Memorial $memorial */
        $memorial = $manager->create();

        $form = $this->createForm(MemorialType::class, $memorial);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->save($memorial);

            return $this->redirectToRoute('memorials_config', [
                'id' => $memorial->getId()
            ]);
        }

        return $this->render('admin/memorials/form.html.twig', [
            'form' => $form->createView(),
            'memorial' => $memorial
        ]);
    }

    /**
     * @Route("/{id}/update", name="memorials_update")
     */
    public function updateAction(Request $request, Memorial $memorial)
    {
        $manager = $this->manager('memorial');

        $form = $this->createForm(MemorialType::class, $memorial);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->save($memorial);

            $this->syncPublishMemorial($memorial);

            $this->setNotice('Memorial atualizado com sucesso.');

            return $this->redirectToRoute('memorials');
        }

        return $this->render('admin/memorials/form.html.twig', [
            'form' => $form->createView(),
            'memorial' => $memorial
        ]);
    }

    /**
     * @Route("/{id}/clone", name="memorials_clone")
     * @Method("post")
     */
    public function cloneAction(Memorial $memorial)
    {
        $cloner = new MemorialCloner();

        $cloned = $cloner->execute($memorial);

        $this->manager('memorial')->save($cloned);

        return $this->json([
            'memorial' => $cloned->toArray()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="memorials_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Memorial $memorial)
    {
        if(!$memorial->isPending()){
            return $this->json([
                'error' => 'Somente memoriais pendentes podem ser excluídos'
            ], Response::HTTP_CONFLICT);
        }

        $this->manager('memorial')->delete($memorial);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/reverse-engineering", name="memorials_reverse_engineering")
     */
    public function reverseEngineeringAction(Memorial $memorial, Request $request)
    {
        $updated = [];
        if(null == $status = $request->query->get('status')) {

            $manager = $this->manager('range');
            $collector = $this->get('component_collector');

            foreach ($memorial->getRanges() as $range) {

                $component = $collector->fromCode($range->getCode());
                $price = $range->getPrice();

                if ($component instanceof ComponentInterface && $component->getCmvApplied() && $price) {

                    $cmvApplied = $component->getCmvApplied();

                    $range
                        ->setTax(Range::DEFAULT_TAX)
                        ->setCostPrice($cmvApplied)
                    ;

                    $markup = ($price * (1 - $range->getTax()) / $range->getCostPrice()) - 1;

                    if($markup < 0) $markup = 0;

                    $range
                        ->setMarkup($markup)
                        ->updatePrice()
                    ;

                    $manager->save($range, false);

                    $updated[] = $range;
                }
            }

            $manager->flush();
        }

        return $this->render('admin/memorials/rev_eng.html.twig', [
            'memorial' => $memorial,
            'updated' => $updated,
            'status' => $status
        ]);
    }

    /**
     * @Route("/{id}/config", name="memorials_config")
     */
    public function configAction(Memorial $memorial, Request $request)
    {
        $levels = Memorial::getDefaultLevels(true);

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
     * @Route("/{id}/product/update", name="memorials_product_update")
     * Method("post")
     */
    public function updateProductAction(Request $request, Memorial $memorial)
    {
        $id = $request->request->get('id');
        $field = $request->request->get('field');
        $value = $request->request->get('value');
        $type = $request->request->get('type');

        $manager = $this->manager($type);

        $component = $manager->find($id);

        if($component instanceof ComponentInterface){

            $accessor = PropertyAccess::createPropertyAccessor();

            $accessor->setValue($component, $field, $value);

            $manager->save($component);

            if('cmvApplied' == $field){

                /** @var \AppBundle\Service\Pricing\RangeNormalizer $normalizer */
                $normalizer = $this->get('range_normalizer');

                $codes = [$component->getCode()];
                $levels = Memorial::getDefaultLevels(true);

                $definitions = [
                    $component->getCode() => [
                        'costPrice' => $component->getCmvApplied()
                    ]
                ];

                $normalizer->normalize($memorial, $codes, $levels, $definitions);
            }
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
     * @Route("/ranges/change", name="memorials_range_update")
     */
    public function updateRangesAction(Request $request)
    {
        $manager = $this->manager('range');
        $data = $request->request->get('ranges');

        foreach ($data as $key => $config){

            $range = $manager->find($config['id']);

            if($range instanceof Range){

                $range
                    ->setMarkup($config['markup'] / 100)
                    ->updatePrice()
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

    /**
     * @param Memorial $memorial
     */
    private function syncPublishMemorial(Memorial $memorial)
    {
        if($memorial->isPublished()){

            $manager = $this->manager('memorial');

            $qb = $manager->createQueryBuilder();

            $qb
                ->where('m.status = :status')
                ->andWhere(
                    $qb->expr()->notIn('m.id', ':id')
                )
                ->setParameters([
                    'status' => Memorial::STATUS_PUBLISHED,
                    'id' => $memorial->getId()
                ]);

            $memorials = $qb->getQuery()->getResult();

            /** @var Memorial $currentMemorial */
            foreach ($memorials as $currentMemorial){
                $currentMemorial->setStatus(Memorial::STATUS_EXPIRED);
                $manager->save($currentMemorial, false);
            }

            $manager->flush();
        }
    }
}
