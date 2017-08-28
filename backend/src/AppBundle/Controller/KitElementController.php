<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\KitElement;
use AppBundle\Form\Component\ElementType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Route("kit-element/{token}")
 */
class KitElementController extends AbstractController
{
    /**
     * @Route("/items", name="kit_element_items")
     */
    public function itemsAction(Kit $kit)
    {
        $this->checkAccess($kit);

        return $this->render('kit.elements', [
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/services", name="kit_element_services")
     */
    public function servicesAction(Kit $kit)
    {
        $this->checkAccess($kit);

        return $this->render('kit.elements', [
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/items/add", name="kit_element_items_add")
     */
    public function addItemAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $element = $this->createElement($kit, KitElement::TYPE_ELEMENT);
        $form = $this->createElementForm($element);

        return $this->processForm($request, $form);
    }

    /**
     * @Route("/services/add", name="kit_element_services_add")
     */
    public function addServiceAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $element = $this->createElement($kit, KitElement::TYPE_SERVICE);
        $form = $this->createElementForm($element);

        return $this->processForm($request, $form);
    }

    /**
     * @Route("/{id}/update", name="kit_element_update")
     */
    public function updateElementAction(Request $request, KitElement $element)
    {
        $kit = $element->getKit();
        $this->checkAccess($kit);

        $form = $this->createElementForm($element);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getKitManager()->save($kit);

            return $this->jsonResponse([
                'updated' => $element->getId()
            ]);
        }

        return $this->render('kit.form_element', [
            'element' => $element,
            'form' => $form->createView(),
            'kit' => $element->getKit()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="kit_element_remove")
     */
    public function deleteElementAction(KitElement $element)
    {
        $this->checkAccess($element->getKit());

        $em = $this->getDoctrine()->getManager();

        $em->remove($element);
        $em->flush();

        return $this->jsonResponse([
            'removed' => $element->getId()
        ]);
    }

    /**
     * @param Kit $kit
     * @param $type
     * @return KitElement
     */
    private function createElement(Kit $kit, $type)
    {
        $element = new KitElement();
        $element
            ->setKit($kit)
            ->setType($type)
        ;

        return $element;
    }

    /**
     * @param KitElement $element
     * @return \Symfony\Component\Form\Form
     */
    private function createElementForm(KitElement $element)
    {
        $route = $element->isService() ? 'kit_element_services_add' : 'kit_element_items_add' ;
        $params = ['token' => $element->getKit()->getToken()];

        if($element->getId()){
            $route = 'kit_element_update';
            $params['id'] = $element->getId();
        }

        $form = $this->createForm(ElementType::class, $element, [
            'action' => $this->generateUrl($route, $params)
        ]);

        return $form;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function processForm(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        $element = $form->getData();

        if($form->isSubmitted() && $form->isValid()){

            $this->getKitManager()->save($element->getKit());

            return $this->jsonResponse([
                'created' => $element->getId()
            ]);
        }

        return $this->render('kit.form_element', [
            'form' => $form->createView(),
            'element' => $element,
            'kit' => $element->getKit()
        ]);
    }

    /**
     * @param Kit $kit
     */
    private function checkAccess(Kit $kit)
    {
        $kitAccount = $kit->getAccount();
        $currentAccount = $this->getCurrentAccount();

        if($kitAccount->getId() != $currentAccount->getId()){
            throw $this->createAccessDeniedException();
        }
    }
}
