<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Project\ProjectInverter;
use AppBundle\Entity\Project\ProjectModule;
use AppBundle\Form\Component\KitInverterType;
use AppBundle\Form\Component\KitModuleType;
use Symfony\Component\HttpFoundation\Request;
USE Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Route("kit/component/{kit}")
 * @ParamConverter("kit", options={"mapping":{"kit":"token"}})
 */
class KitComponentController extends AbstractController
{
    /**
     * @Route("/inverters", name="kit_inverters")
     */
    public function invertersAction(Kit $kit)
    {
        $this->checkAccess($kit);

        return $this->render('kit.inverters', [
            'kit' => $kit,
            'components' => $kit->getInverters()
        ]);
    }

    /**
     * @Route("/modules", name="kit_modules")
     */
    public function modulesAction(Kit $kit)
    {
        $this->checkAccess($kit);

        return $this->render('kit.modules', [
            'kit' => $kit,
            'components' => $kit->getModules()
        ]);
    }

    /**
     * @Route("/inverter/add", name="kit_inverter_add")
     * @Method({"get","post"})
     */
    public function addInverterAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $component = new KitComponent();
        $component->setKit($kit);

        $form = $this->createForm(KitInverterType::class, $component);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $component->getInverter()){

            $this->getKitManager()->save($component->getKit());

            return $this->jsonResponse([
                'module' => $component->getId()
            ], Response::HTTP_CREATED);
        }
        
        return $this->render('kit.form_inverter', [
            'form' => $form->createView(),
            'component' => $component,
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/module/add", name="kit_module_add")
     * @Method({"get","post"})
     */
    public function addModuleAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $component = new KitComponent();
        $component->setKit($kit);

        $form = $this->createForm(KitModuleType::class, $component);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $component->getModule()){

            $this->getKitManager()->save($component->getKit());

            return $this->jsonResponse([
                'module' => $component->getId()
            ], Response::HTTP_CREATED);
        }

        return $this->render('kit.form_module', [
            'component' => $component,
            'form' => $form->createView(),
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/{id}/update", name="kit_component_update")
     * @Method({"get","post"})
     */
    public function updateComponentAction(Request $request, KitComponent $component)
    {
        $this->checkAccess($component->getKit());

        $formType = $component->isInverter() ? KitInverterType::class : KitModuleType::class ;
        $form = $this->createForm($formType, $component);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $this->getKitManager()->save($component->getKit());

            $field = $component->isInverter() ? 'inverter' : 'module' ;

            if(!$request->isXmlHttpRequest()){
                return $this->redirectToRoute('kit_component_update', [
                    'kit' => $component->getKit()->getToken(),
                    'id' => $component->getId()
                ]);
            }

            return $this->jsonResponse([
                $field => $component->getId()
            ],Response::HTTP_NO_CONTENT);
        }

        $formTemplate = $component->isInverter() ? 'kit.form_inverter' : 'kit.form_module' ;

        return $this->render($formTemplate, [
            'component' => $component,
            'form' => $form->createView(),
            'kit' => $component->getKit()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="kit_component_delete")
     * @Method("delete")
     */
    public function deleteComponentAction(Request $request, KitComponent $component)
    {
        $em = $this->getDoctrine()->getManager();

        $isInverter = $component->isInverter();
        $targetClass = $isInverter ? ProjectInverter::class : ProjectModule::class ;
        $targetField = $isInverter ? 'inverter' : 'module' ;

        $distributions = $em->getRepository($targetClass)->findBy([$targetField => $component]);

        if(0 < $count = count($distributions)){
            return $this->jsonResponse([
                'error' => $this->translate('kit.error.project_has_component', [
                    '%component%' => $isInverter ? 'inversor' : 'módulo',
                    '%count%' => $count
                ])
            ], Response::HTTP_IM_USED);
        }

        $kit = $component->getKit();

        $this->checkAccess($kit);

        $countCurrent = $component->isInverter() ? $kit->countInverters() : $kit->countModules() ;
        if($countCurrent <= 1){
            return $this->jsonResponse([
                'error' => 'O Kit deve possuir pelo menos 1 ' .($component->isInverter() ? 'Inversor' : 'Módulo')
            ], Response::HTTP_IM_USED);
        }

        $kit->removeComponent($component);

        $em->remove($component);

        $this->getKitManager()->save($kit);

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
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
