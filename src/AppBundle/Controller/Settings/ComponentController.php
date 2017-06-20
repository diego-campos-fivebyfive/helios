<?php

namespace AppBundle\Controller\Settings;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Module;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @Route("components")
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Component management", routeName="components")
 */
class ComponentController extends AbstractController
{
    /**
     * @Route("/", name="components")
     */
    public function indexAction()
    {
        return $this->render('settings.component\index');
    }

    /**
     * @Breadcrumb("{context} published")
     * @Route("/{context}/published", name="components_published")
     */
    public function publishedAction(Request $request, $context)
    {
        if (!in_array($context, ['inverter', 'module'])) {
            throw $this->createNotFoundException('Invalid component context');
        }

        $managerGetter = 'get' . ucfirst($context) . 'Manager';

        /** @var \Sonata\CoreBundle\Model\BaseEntityManager $manager */
        $manager = $this->$managerGetter();

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from($manager->getClass(), 'c')
            ->join('c.maker', 'm', 'WITH')
            ->where('c.status = :status')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('c.model', 'asc')
            ->setParameters([
                'status' => ComponentInterface::STATUS_PUBLISHED
            ]);

        $powerField = ('module' == $context) ? 'c.maxPower' : 'c.nominalPower';

        $this->makerQueryBuilderFilter($qb, $request, $powerField);

        /*$datasheet = $request->get('datasheet');
        $image = $request->get('image');*/

        if("" != $datasheet = $request->get('datasheet', null)){
            $qb->andWhere($datasheet ? 'c.datasheet is not null' : 'c.datasheet is null');
        }

        if("" != $image = $request->get('image', null)){
            $qb->andWhere($image ? 'c.image is not null' : 'c.image is null');
        }

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('settings.component\published', [
            'pagination' => $pagination,
            'context' => $context,
            'datasheet' => $datasheet,
            'image' => $image
        ]);
    }

    /**
     * @Route("/list/{status}", name="components_list")
     */
    public function featuredAction($status)
    {
        $criteria = ['status' => $status];

        $modules = $this->getModuleManager()->findBy($criteria);
        $inverters = $this->getInverterManager()->findBy($criteria);

        return $this->render('settings.component\grid', [
            'status' => $status,
            'modules' => $modules,
            'inverters' => $inverters
        ]);
    }

    /**
     * @Route("/publish/{token}/m", name="publish_module")
     * @Method("post")
     */
    public function publishModuleAction(Module $module)
    {
        if (!$module->isValidated()) {
            return $this->responsePublishStatusError();
        }

        $module->setStatus(Module::STATUS_PUBLISHED);

        $this->getModuleManager()->save($module);

        $this->publishMakerComponent($module);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/unpublish/{token}/m", name="unpublish_module")
     * @Method("post")
     */
    public function unPublishModuleAction(Module $module)
    {
        $usages = $this->getComponentUsages($module);

        if (count($usages)) {
            return $this->responseUsageError();
        }

        if ($module->isPublished()) {

            $module->setStatus(Module::STATUS_VALIDATED);

            $this->getModuleManager()->save($module);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/ignore/{token}/m", name="ignore_module")
     * @Method("post")
     */
    public function ignoreModuleAction(Module $module)
    {
        if ($module->isFeatured()) {

            $module->setStatus(Module::STATUS_IGNORED);

            $this->getModuleManager()->save($module);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{token}/m", name="delete_module")
     * @Method("delete")
     */
    public function deleteModuleAction(Module $module)
    {
        if (!$module->isValidated()) {
            return $this->responseDeleteStatusError();
        }

        $usages = $this->getComponentUsages($module);

        if (count($usages)) {
            return $this->responseUsageError();
        }

        $this->getModuleManager()->delete($module);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/publish/{token}/i", name="publish_inverter")
     * @Method("post")
     */
    public function publishInverterAction(Inverter $inverter)
    {
        if (!$inverter->isValidated()) {
            return $this->responsePublishStatusError();
        }

        $inverter->setStatus(Inverter::STATUS_PUBLISHED);

        $this->getInverterManager()->save($inverter);

        $this->publishMakerComponent($inverter);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/unpublish/{token}/i", name="unpublish_inverter")
     * @Method("post")
     */
    public function unPublishInverterAction(Inverter $inverter)
    {
        $usages = $this->getComponentUsages($inverter);

        if (count($usages)) {
            return $this->responseUsageError();
        }

        if ($inverter->isPublished()) {

            $inverter->setStatus(Inverter::STATUS_VALIDATED);

            $this->getInverterManager()->save($inverter);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/ignore/{token}/i", name="ignore_inverter")
     * @Method("post")
     */
    public function ignoreInverterAction(Inverter $inverter)
    {
        if ($inverter->isFeatured()) {

            $inverter->setStatus(Inverter::STATUS_IGNORED);

            $this->getInverterManager()->save($inverter);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{token}/i", name="delete_inverter")
     * @Method("delete")
     */
    public function deleteInverterAction(Inverter $inverter)
    {
        if (!$inverter->isValidated()) {
            return $this->responseDeleteStatusError();
        }

        $usages = $this->getComponentUsages($inverter);

        if (count($usages)) {
            return $this->responseUsageError();
        }

        $this->getInverterManager()->delete($inverter);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function responsePublishStatusError()
    {
        return $this->jsonResponse([
            'error' => $this->translate('Only validated components can be published')
        ], Response::HTTP_CONFLICT);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function responseDeleteStatusError()
    {
        return $this->jsonResponse([
            'error' => $this->translate('Only validated components can be excluded')
        ], Response::HTTP_CONFLICT);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function responseUsageError()
    {
        return $this->jsonResponse([
            'error' => $this->translate('There are kits configured with this component')
        ], Response::HTTP_IM_USED);
    }

    /**
     * @param ComponentInterface $component
     * @return array
     */
    private function getComponentUsages(ComponentInterface $component)
    {
        $em = $this->getDoctrine()->getManager();

        $usages = $em->getRepository(KitComponent::class)->findBy([
            $component->isModule() ? 'module' : 'inverter' => $component->getId()
        ]);

        return $usages;
    }

    /**
     * @param QueryBuilder $qb
     * @param Request $request
     * @param $powerField
     */
    private function makerQueryBuilderFilter(QueryBuilder &$qb, Request $request, $powerField)
    {
        $filterTokens = explode(' ', $request->query->get('filter_value'));
        if (count($filterTokens) > 1) {

            $subFilters = [];
            foreach ($filterTokens as $filterToken) {
                $subFilters[] = $qb->expr()->orX(
                    $qb->expr()->like('m.name', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like('c.model', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like($powerField, $qb->expr()->literal('%' . $filterToken . '%'))
                );
            }

            $filterX = $qb->expr()->andX();
            $filterX->addMultiple($subFilters);
            $qb->andWhere($filterX);

            unset($_GET['filter_name'], $_GET['filter_value']);

        } else {
            $this->overrideGetFilters();
        }
    }

    /**
     * Remove a maker's reference to make it global
     * @param ComponentInterface $component
     */
    private function publishMakerComponent(ComponentInterface $component)
    {
        if ($component->isPublished()) {
            $maker = $component->getMaker();

            if ($maker->getAccount()) {
                $maker->setAccount(null);

                $this->getMakerManager()->save($maker);
            }
        }
    }
}