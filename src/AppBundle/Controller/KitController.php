<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Kit;
use AppBundle\Form\Component\KitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
USE Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 *
 * #//@Security("has_role('ROLE_OWNER')")
 *
 * @Route("kit")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Kits", route={"name"="kit_index"})
 */
class KitController extends AbstractController
{
    /**
     * @Route("/generate", name="kit_generator")
     * @Method({"get", "post"})
     */
    public function generateAction(Request $request)
    {
        return $this->render('kit.generate');
    }

    /**
     * @Route("/map", name="map_generator")
     * @Method({"get", "post"})
     */
    public function generateMap(Request $request)
    {
        return $this->render('kit.generate_map');
    }


    /**
     * @Route("/", name="kit_index")
     */
    public function indexAction(Request $request)
    {
        /*$manager = $this->getKitManager();
        $account = $this->getCurrentAccount();

        $kits = $manager->findBy(['account' => $account]);*/

        $template = $request->isXmlHttpRequest() ? 'kit.kits' : 'kit.index';

        $this->clearTemplateCache($template);

        $kits = [];

        return $this->render($template, array(
            'kits' => $kits
        ));
    }

    /**
     * @Route("/create", name="kit_create")
     * @Method({"get", "post"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->getKitManager();

        /** @var Kit $kit */
        $kit = $manager->create();
        $kit
            ->addAttribute('index', $this->incrementAccountIndex('kit_index'))
            ->setAccount($this->getCurrentAccount());

        // Woopra Event
        $event = $this->createWoopraEvent('novo kit', [
            'numero' => $kit->getNumber()
        ]);

        $manager->save($kit);

        return $this->redirectToRoute('kit_manage', [
            'token' => $kit->getToken(),
            'woopra_event' => $event->getId()
        ]);
    }

    /**
     * @Route("/{token}/m", name="kit_manage")
     * @Method({"GET", "POST"})
     * @Breadcrumb("Gerenciar Kit - id: {kit.attribute:index}", routeName="kit_manage", routeParameters={"token"="{token}"})
     */
    public function manageAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $form = $this->createForm(KitType::class, $kit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getKitManager()->save($kit);

            if ($request->isXmlHttpRequest()) {

                return $this->jsonResponse([
                    'final_cost' => $this->renderView('kit.final_cost', ['kit' => $kit]),
                    'total_cost' => $this->renderView('kit.total_cost', ['kit' => $kit])
                ]);
            }
        }

        return $this->render('kit.manage', array(
            'kit' => $kit,
            'form' => $form->createView(),
            'woopraEvent' => $this->requestWoopraEvent($request)
        ));
    }

    /**
     * @Route("/{token}/price_detailing", name="kit_price_detailing")
     */
    public function priceDetailingAction(Kit $kit)
    {
        return $this->render('kit.price_detailing', [
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/{token}/margin_detailing", name="kit_margin_detailing")
     */
    public function marginDetailingAction(Kit $kit)
    {
        //$pricingParameters = $this->get('app.kit_pricing_manager')->findAll();
        //$kit->setPricingParameters($pricingParameters);

        return $this->render('kit.margin_detailing', [
            'kit' => $kit
        ]);
    }

    /**
     * @Route("/{token}/_form", name="kit_form_cost")
     * @Method({"GET", "POST"})
     */
    public function formCostAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);
        $form = $this->createForm(KitType::class, $kit, [
            'final' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getKitManager()->save($kit);

            return $this->jsonResponse([], Response::HTTP_ACCEPTED);
        }

        return $this->render('kit.form_cost', [
            'kit' => $kit,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{token}/costs", name="kit_costs")
     */
    public function costsAction(Kit $kit)
    {
        return $this->jsonResponse([
            'final_cost' => $this->renderView('kit.final_cost', ['kit' => $kit]),
            'total_cost' => $this->renderView('kit.total_cost', ['kit' => $kit])
        ]);
    }

    /**
     * @Route("/{token}/delete", name="kit_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Kit $kit)
    {
        $this->checkAccess($kit);

        $projects = $this->getProjectManager()->findBy([
            'kit' => $kit
        ]);

        if (0 != $count = count($projects)) {
            return $this->jsonResponse([
                'error' => sprintf('Este Kit está sendo utilizado em %s dimensionamentos!', $count)
            ], Response::HTTP_IM_USED);
        }

        $this->getKitManager()->delete($kit);

        return $this->jsonResponse([
            'success' => $kit->getId()
        ]);
    }

    /**
     * @param Kit $kit
     */
    private function checkAccess(Kit $kit)
    {
        $kitAccount = $kit->getAccount();
        $currentAccount = $this->getCurrentAccount();

        if ($kitAccount->getId() != $currentAccount->getId()) {
            throw $this->createAccessDeniedException();
        }
    }
}
