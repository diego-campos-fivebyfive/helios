<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Form\Component\GeneratorType;
use AppBundle\Form\Order\ElementType;
use AppBundle\Form\Order\OrderType;
use AppBundle\Service\ProjectGenerator\ShippingRuler;
use AppBundle\Form\Financial\ShippingType;
use AppBundle\Service\Order\ElementResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Security("has_role('ROLE_OWNER') or has_role('ROLE_OWNER_MASTER')")
 * @Route("project/generator")
 * @Breadcrumb("Orçamentos")
 */
class ProjectGeneratorController extends AbstractController
{
    /**
     * @Route("/", name="project_generator")
     */
    public function indexAction(Request $request)
    {
        $id = $request->query->getInt('order', 0);
        $manager = $this->manager('order');
        $order = $manager->find($id);

        if (!$order) {
            $order = $this->getOrderValid($manager);

            return $this->redirectOrder($order);
        }

        if ($order->getSendAt() || $order->getParent()) {
            $order = $this->getOrderValid($manager);

            return $this->redirectOrder($order);
        }

        return $this->render('generator.index', [
            'order' => $order
        ]);
    }

    public function redirectOrder($order)
    {
        return $this->redirectToRoute('project_generator', [
            'order' => $order->getId()
        ]);
    }

    public function getOrderValid($manager)
    {
        $account = $this->account();

        $order = $manager->findOneBy([
            'account' => $account,
            'sendAt' => null,
            'parent' => null
        ]);

        if (!$order) {
            /** @var Order $order */
            $order = $manager->create();
            $order->setAccount($account);

            $manager->save($order);
        }

        return $order;
    }

    /**
     * @Route("/{id}/components", name="generator_components")
     */
    public function componentsAction(Project $project)
    {
        $this->getGenerator()->pricing($project);

        return $this->render('generator.components', [
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
     * @Route("/{id}/shipping", name="generator_financial_shipping")
     */
    public function shippingAction(Request $request, Order $order)
    {
        $rule = $order->getShippingRules();
        $form = $this->createForm(ShippingType::class, $rule);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $rule = $form->getData();

            ShippingType::normalize($rule);

            $rule['price'] = $order->getTotal();
            $rule['power'] = $order->getPower();

            ShippingRuler::apply($rule);

            $order->setShippingRules($rule);

            $this->manager('order')->save($order);

            return $this->json([
                'shipping' => $order->getShipping()
            ]);
        }

        return $this->render('generator.financial_shipping', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders", name="generator_orders")
     */
    public function ordersAction()
    {
        $manager = $this->manager('order');

        $account = $this->account();
        $order = $manager->findOneBy([
            'account' => $account,
            'sendAt' => null,
            'parent' => null
        ]);

        return $this->render('generator.orders', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/orders/{id}", name="generator_orders_create")
     * @Method("post")
     */
    public function createOrderAction(Project $project)
    {
        $transformer = $this->get('order_transformer');
        $manager = $this->manager('order');

        $account = $this->account();
        $order = $manager->findOneBy([
            'account' => $account,
            'sendAt' => null,
            'parent' => null
        ]);

        /** @var OrderInterface $order */
        $orderChildren = $transformer->transformFromProject($project);
        $order->addChildren($orderChildren);

        $this->get('order_precifier')->precify($orderChildren);

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
        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           $this->manager('order')->save($order);

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
        $this->manager('order')->delete($order);

        return $this->json([]);
    }

    /**
     * @Route("/orders/{id}/elements", name="generator_orders_elements")
     */
    public function getOrderElementsAction(Order $order)
    {
        return $this->render('generator.order_elements', [
            'order' => $order
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

        $form = $this->createForm(ElementType::class, $element, [
            'manager' => $componentManager
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $component = $componentManager->findOneBy(['code' => $element->getCode()]);

            ElementResolver::resolve($component, $element);

            $this->get('order_precifier')->precify($order);

            return $this->json([], Response::HTTP_CREATED);
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

        $form = $this->createForm(ElementType::class, $element, [
            'manager' => $componentManager
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $component = $componentManager->findOneBy(['code' => $element->getCode()]);

            ElementResolver::resolve($element, $component);

            $manager->save($element);

            $this->get('order_precifier')->precify($element->getOrder());

            return $this->json([], Response::HTTP_ACCEPTED);
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

        $this->get('order_precifier')->precify($element->getOrder());

        return $this->json([]);
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }
}