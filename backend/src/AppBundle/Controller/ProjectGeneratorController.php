<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Parameter;
use AppBundle\Entity\TimelineInterface;
use AppBundle\Form\Component\GeneratorType;
use AppBundle\Form\Financial\CompanyType;
use AppBundle\Form\Order\DeliveryType;
use AppBundle\Form\Order\DiscountType;
use AppBundle\Form\Order\ElementType;
use AppBundle\Form\Order\OrderType;
use AppBundle\Service\Order\OrderFinder;
use AppBundle\Service\ProjectGenerator\ShippingRuler;
use AppBundle\Form\Financial\ShippingType;
use AppBundle\Service\Order\ElementResolver;
use AppBundle\Util\Validator\Document;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @Security("has_role('ROLE_OWNER') or has_role('ROLE_PLATFORM_AFTER_SALES') or has_role('ROLE_PLATFORM_EXPANSE')")
 * @Route("twig/project/generator")
 * @Breadcrumb("Orçamentos")
 */
class ProjectGeneratorController extends AbstractController
{
    /**
     * @Route("/", name="project_generator")
     */
    public function indexAction(Request $request)
    {
        if (0 == $id = $request->query->getInt('order')) {
            return $this->resolveOrderReference($request);
        }

        /** @var Order $order */
        $order = $this->manager('order')->find($id);

        if (is_null($order)) {
            throw $this->createAccessDeniedException('Order not found');
        }

        if (!in_array($order->getStatus(), [Order::STATUS_BUILDING, Order::STATUS_PENDING, Order::STATUS_VALIDATED])) {
            throw $this->createAccessDeniedException('Order with this status can not be edited');
        }

        $accountLevel = $order->getAccount()->getLevel();

        $this->denyAccessUnlessGranted('edit', $order);

        if ($order->isChildren()) {
            throw $this->createAccessDeniedException('Only master order can be edited');
        }

        $this->synchronizerOrder($order);

        $expired = $this->checkMemorial()->getPublishedAt() > $order->getCreatedAt();

        if(!$this->checkOrderStatus($order) || $expired) {
            return $this->render('generator.error', [
                'order' => $order,
                'expired' => $expired
            ]);
        }

        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        return $this->render('generator.index', [
            'order' => $order,
            'member' => $this->member(),
            'level' => $accountLevel,
            'couponEnabled' => $parameter->get('coupon_order_rescue')
        ]);
    }

    /**
     * @Route("/{id}/components", name="generator_components")
     */
    public function componentsAction(Project $project, Request $request)
    {
        if(null != $level = $request->query->get('level') && Project::SOURCE_CATALOG == $project->getSource()){
            $project->setLevel($level);
        }

        $this->getGenerator()->pricing($project);

        return $this->render('generator.components', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/position", name="generator_position")
     */
    public function positionAction(Project $project)
    {
        $view = $project->getDefaults()['roof_type'] == ProjectInterface::GROUND_STRUCTURE ? 'generator.disposition' : 'generator.position';

        return $this->render($view, [
            'project' => $project
        ]);
    }

    /**
     * @Route("/project", name="order_generator_project")
     */
    public function projectAction(Request $request)
    {
        $generator = $this->getGenerator();

        if(null != $id = $request->get('id')){
            $project = $this->manager('project')->find($id);
            $defaults = $project->getDefaults();
        }else {

            /** @var ProjectInterface $project */
            $project = $this->manager('project')->create();

            $defaults = $generator->loadDefaults([
                'source' => 'power'
            ]);

            $project->setMember($this->member());
        }

        $form = $this->createForm(GeneratorType::class, $defaults, []);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($project->getId()) {
                $generator->autoSave(false);
                $generator->reset($project);
            }

            $project->setDefaults($form->getData());
            $generator->generate($project);

            $generator->pricing($project);

            $errors = [];
            $defaults  = $project->getDefaults();
            if(count($defaults['errors'])){
                if(in_array('exhausted_inverters', $defaults['errors'])) {
                    $errors[] = 'Número máximo de inversores excedido!';
                }
            }

            return $this->json([
                'errors' => $errors,
                'project' => [
                    'id' => $project->getId(),
                    'power' => $project->getPower()
                ]
            ]);
        }

        return $this->render('generator.generate', [
            'form' => $form->createView(),
            'project' => $project,
            'errors' => $form->getErrors(true)->count()
        ]);
    }

    /**
     * @Route("/{id}/message", name="generator_order_message")
     *
     * @Method("post")
     */
    public function messageAction(Request $request, Order $order)
    {
        $order->setMessage($request->get('message'));

        $this->manager('order')->save($order);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/discount", name="generator_order_discount")
     */
    public function discountAction(Request $request, Order $order)
    {
        $discount = $order->getDiscountConfig();

        $maxCommercialDiscount = $this->findSettings()->get('max_commercial_discount', 0);

        $form = $this->createForm(DiscountType::class, $discount, [
            'member' => $this->member()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discount = $form->getData();

            $order->setDiscountConfig($discount);
            $this->manager('order')->save($order);

            $this->get('order_manipulator')->normalizeInfo($order);

            return $this->json([
                'total' => $order->getTotal(),
                'orderDiscount' => $order->getDiscount()
            ]);
        }

        return $this->render('admin/orders/form_discount.html.twig',[
            'order' => $order,
            'maxDiscount' => $maxCommercialDiscount,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/shipping", name="generator_financial_shipping")
     */
    public function shippingAction(Request $request, Order $order)
    {
        $rule = $order->getShippingRules();

        $form = $this->createForm(ShippingType::class, $rule, [
            'member' => $this->member(),
            'order' => $order,
            'isProject' => false,
            'rule' => $rule,
            'parameters' => $this->findSettings()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $rule = $form->getData();

            ShippingType::normalize($rule);

            $rule['price'] = $order->getSubTotal();
            $rule['power'] = $order->getPower();

            $this->calculateShipping($order, $rule);

            return $this->json([
                'shipping' => $order->getShipping(),
                'total' => $order->getTotal(),
                'totalExcDiscount' => $order->getTotalExcDiscount()
            ]);
        }

        return $this->render('generator.financial_shipping', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/company", name="generator_company_shipping")
     */
    public function companyShippingAction(Request $request, Order $order)
    {
        $rule = $order->getShippingRules();
        if (!array_key_exists('company', $rule) || !is_array($rule['company'])) {
            $rule['company'] = [];
        }

        $form = $this->createForm(CompanyType::class, $rule['company']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rule['company'] = $form->getData();

            $order->setShippingRules($rule);

            $this->manager('order')->save($order);

            return $this->json([]);
        }

        return $this->render('generator.company_shipping', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delivery", name="generator_delivery_address")
     */
    public function deliveryAddressAction(Request $request, Order $order)
    {
        $form = $this->createForm(DeliveryType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager('order')->save($order);

            $this->get('order_timeline')->create($order, TimelineInterface::TAG_DELIVERY_ADDRESS);

            return $this->json([]);
        }

        return $this->render('generator.delivery_address', [
           'order' => $order,
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders/{id}", name="generator_orders")
     */
    public function ordersAction(Order $order)
    {
        return $this->render('generator.orders', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/orders/list/{id}", name="generator_orders_list")
     */
    public function ordersListAction(Order $order)
    {
        return $this->render('generator.review_orders', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/orders/{id}/create", name="generator_orders_create")
     * @Method("post")
     */
    public function createOrderAction(Request $request, Project $project)
    {
        $manager = $this->manager('order');

        /** @var Order $master */
        $master = $manager->find($request->get('master'));

        $transformer = $this->get('order_transformer');

        /** @var OrderInterface $order */
        $order = $transformer->transformFromProject($project);
        $order->setParent($master);

        $this->get('order_precifier')->precify($order);

        $this->manager('project')->delete($project);

        return $this->json([
            'order' => [
                'id' => $order->getId()
            ]
        ]);
    }

    /**
     * @Route("/orders/{id}/update", name="generator_orders_update")
     */
    public function updateOrderAction(Request $request, Order $order)
    {
        $form = $this->createForm(OrderType::class, $order, array(
            'member' => $this->member()
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           $this->manager('order')->save($order);

            $this->get('order_precifier')->precify($order);

           return $this->json([]);
        }

        return $this->render('generator.order', [
            'form' => $form->createView(),
            'order' => $order
        ]);
    }


    /**
     * @Route("/orders/{id}/delete", name="generator_orders_delete")
     * @Method("delete")
     */
    public function deleteOrderAction(Order $order)
    {
        $manager = $this->manager('order');
        $parent = $order->getParent();

        $manager->delete($order);

        return $this->json([]);
    }

    /**
     * @Route("/orders/{id}/elements", name="generator_orders_elements")
     */
    public function getOrderElementsAction(Order $order)
    {
        $maxOrderDiscount = $this->findSettings()->get('max_order_discount');

        return $this->render('generator.order_elements', [
            'order' => $order,
            'maxOrderDiscount' => $maxOrderDiscount
        ]);
    }

    /**
     * @Route("/orders/{id}/elements/create/{type}", name="generator_orders_element_create")
     */
    public function createOrderElementAction(Order $order, $type, Request $request)
    {
        $componentManager = $this->manager($type);
        $manager = $this->manager('order_element');

        $element = $manager->create();
        $element->setOrder($order);

        $promo = $order->isPromotional();

        $form = $this->createForm(ElementType::class, $element, [
            'manager' => $componentManager,
            'promocional' => $promo,
            'member' => $this->member()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $component = $componentManager->findOneBy(['code' => $element->getCode()]);

            ElementResolver::resolve($component, $element);

            $this->finishElement($element);

            return $this->json([
                'description' => $order->getDescription()
            ], Response::HTTP_CREATED);
        }

        return $this->render('generator.element', [
            'form' => $form->createView(),
            'element' => $element
        ]);
    }

    /**
     * @Route("/orders/elements/{id}/update", name="generator_orders_element_update")
     */
    public function updateOrderElementAction(Element $element, Request $request)
    {
        $componentManager = $this->manager($element->getFamily());
        $manager = $this->manager('order_element');

        $promo = $element->getOrder()->isPromotional();

        $form = $this->createForm(ElementType::class, $element, [
            'manager' => $componentManager,
            'promocional' => $promo,
            'member' => $this->member()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $component = $componentManager->findOneBy(['code' => $element->getCode()]);

            ElementResolver::resolve($element, $component);

            $manager->save($element);

            $this->finishElement($element);

            return $this->json([
                'total' => $element->getOrder()->getTotal(),
                'power' => $element->getOrder()->getPower(),
                'description' => $element->getOrder()->getDescription()
            ], Response::HTTP_ACCEPTED);
        }

        return $this->render('generator.element', [
            'form' => $form->createView(),
            'element' => $element
        ]);
    }

    /**
     * @Route("/orders/element/{id}/delete", name="generator_orders_element_delete")
     * @Method("delete")
     */
    public function deleteOrderElementAction(Element $element)
    {
        $this->manager('order_element')->delete($element);

        $this->finishElement($element);

        $order = $element->getOrder();

        return $this->json([
            'total' => $order->getTotal(),
            'description' => $order->getDescription()
        ]);
    }

    /**
     * @Route("/cnpj/validate", name="order_cnpj_validate")
     */
    public function cnpjValidateAction(Request $request)
    {
        $status = Response::HTTP_OK;

        if (!Document::isCnpj($request->get('cnpj'))) {
            $status = Response::HTTP_IM_USED;
        }

        return $this->json([],$status);
    }

    private function finishElement(Element $element)
    {
        $order = $element->getOrder();

        $this->get('order_precifier')->precify($order);

        $parent = $order->getParent();

        $this->calculateShipping($parent);
    }

    /**
     * @param Order $order
     * @param array $rule
     */
    private function calculateShipping(Order $order, array $rule = [])
    {
        if (empty($rule)) {
            $rule = $order->getShippingRules();
            $rule['price'] = $order->getSubTotal();
        }

        ShippingRuler::apply($rule);

        $order->setShippingRules($rule);

        $this->manager('order')->save($order);

        $this->get('order_manipulator')->normalizeInfo($order);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function resolveOrderReference(Request $request)
    {
        if(null != $accountId = $request->query->get('account')){
            $account = $this->manager('account')->find($accountId);
        }else{
            $account = $this->account();
        }

        $manager = $this->manager('order');
        $member = $this->member();
        $isPlatform = $member->isPlatformUser();

        $criteria = [
            'status' => Order::STATUS_BUILDING,
            'source' => $isPlatform ? Order::SOURCE_PLATFORM : Order::SOURCE_ACCOUNT,
            'account' => $account
        ];

        if($isPlatform){
            $criteria['agent'] = $member;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $order = $manager->create();
        foreach ($criteria as $property => $value){
            $accessor->setValue($order, $property, $value);
        }

        $manager->save($order);

        $this->get('order_timeline')->create($order);

        return $this->redirectToRoute('project_generator', [
            'order' => $order->getId()
        ]);
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function checkOrderStatus(Order $order)
    {
        $isPlatform = $this->user()->isPlatform();
        $lockStatus = in_array($order->getStatus(), [Order::STATUS_APPROVED, Order::STATUS_REJECTED, Order::STATUS_DONE]);

        if ($lockStatus || (!$isPlatform && $order->isPending()) || ($isPlatform && $order->isValidated())) {
            return false;
        }

        return true;
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }

    /**
     * @return Parameter
     */
    private function findSettings()
    {
        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        return $parameter;
    }

    /**
     * @return \AppBundle\Entity\Pricing\MemorialInterface
     */
    private function checkMemorial()
    {
        $memorial = $this->container->get('memorial_loader')->load();

        if(!$memorial){
            throw new \InvalidArgumentException('Memorial not loaded');
        }

        return $memorial;
    }

    /**
     * @param OrderInterface $order
     */
    private function synchronizerOrder(OrderInterface $order)
    {
        if (!$this->user()->isPlatformAdmin() && !$this->user()->isPlatformMaster()) {
            foreach ($order->getChildrens() as $children) {
                $this->get('additive_synchronizer')->synchronize($children);
            }
        }

    }
}
