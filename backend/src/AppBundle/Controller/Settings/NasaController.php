<?php

namespace AppBundle\Controller\Settings;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Project\NasaCatalog;
use AppBundle\Entity\Project\NasaCatalogInterface;
use AppBundle\Form\Project\NasaCatalogType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Security("has_role('ROLE_OWNER')")
 * @Route("/nasa")
 * @Breadcrumb("Climatic data", route={"name"="nasa"})
 */
class NasaController extends AbstractController
{
    /**
     * @Route("/", name="nasa")
     */
    public function indexAction()
    {
        return $this->render('settings.nasa\catalog');
    }

    /**
     * @Route("/info", name="nasa_info")
     */
    public function infoAction(Request $request)
    {
        $latitude = floor($request->get('latitude'));
        $longitude = floor($request->get('longitude'));

        $provider = $this->getNasaProvider();

        $radiationGlobal = $provider->radiationGlobal($latitude, $longitude);
        $radiationDiffuse = $provider->radiationDiffuse($latitude, $longitude);
        $radiationAtmosphere = $provider->radiationAtmosphere($latitude, $longitude);
        $airTemperature = $provider->airTemperature($latitude, $longitude);

        $accountCatalog = $this->getNasaInfo(NasaCatalog::RADIATION_GLOBAL, $latitude, $longitude);

        if($accountCatalog->getAccount()){
            return $this->updateAction($request);
        }

        return $this->render('settings.nasa\info', [
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'information' => [
                'radiation_global' => $radiationGlobal,
                //'radiation_diffuse' => $radiationDiffuse,
                //'radiation_atmosphere' => $radiationAtmosphere,
                'air_temperature' => $airTemperature
            ]
        ]);
    }

    /**
     * @Route("/update", name="nasa_update")
     */
    public function updateAction(Request $request)
    {
        $latitude = floor($request->get('latitude'));
        $longitude = floor($request->get('longitude'));

        $radiationGlobal = $this->loadOrCreate(NasaCatalog::RADIATION_GLOBAL, $latitude, $longitude);
        $radiationDiffuse = $this->loadOrCreate(NasaCatalog::RADIATION_DIFFUSE, $latitude, $longitude);
        $radiationAtmosphere = $this->loadOrCreate(NasaCatalog::RADIATION_ATMOSPHERE, $latitude, $longitude);
        $airTemperature = $this->loadOrCreate(NasaCatalog::AIR_TEMPERATURE, $latitude, $longitude);

        $formRadiationGlobal = $this->createFormCatalog($radiationGlobal);
        $formRadiationDiffuse = $this->createFormCatalog($radiationDiffuse);
        $formRadiationAtmosphere = $this->createFormCatalog($radiationAtmosphere);
        $formAirTemperature = $this->createFormCatalog($airTemperature);

        return $this->render('settings.nasa\form', [
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'forms' => [
                'radiation_global' => $formRadiationGlobal->createView(),
                //'radiation_diffuse' => $formRadiationDiffuse->createView(),
                //'radiation_atmosphere' => $formRadiationAtmosphere->createView(),
                'air_temperature' => $formAirTemperature->createView()
            ]
        ]);
    }

    /**
     * @Route("/{id}/save", name="nasa_save")
     */
    public function saveAction(NasaCatalog $nasaCatalog, Request $request)
    {
        $form = $this->createFormCatalog($nasaCatalog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getNasaProvider()->save($nasaCatalog);

            return $this->jsonResponse([]);
        }

        return $this->jsonResponse([]);
    }

    /**
     * @Route("/recycle", name="nasa_recycle")
     * @Method("delete")
     */
    public function recycleAction(Request $request)
    {
        $latitude = floor($request->get('latitude'));
        $longitude = floor($request->get('longitude'));

        $contexts = [
            NasaCatalog::RADIATION_GLOBAL,
            NasaCatalog::RADIATION_DIFFUSE,
            NasaCatalog::RADIATION_ATMOSPHERE,
            NasaCatalog::AIR_TEMPERATURE,
            NasaCatalog::AIR_TEMPERATURE_MIN,
            NasaCatalog::AIR_TEMPERATURE_MAX
        ];

        $provider = $this->getNasaProvider();

        $currentAccount = $this->getCurrentAccount();

        foreach($contexts as $context){
            $catalog = $this->getNasaInfo($context, $latitude, $longitude);
            if($catalog instanceof NasaCatalog){
                if(null != $account = $catalog->getAccount()){
                    if($account->getId() == $currentAccount->getId()){

                        $provider->delete($catalog);
                    }
                }
            }
        }

        return $this->jsonResponse([]);
    }

    /**
     * @param NasaCatalog $nasaCatalog
     * @return \Symfony\Component\Form\Form
     */
    private function createFormCatalog(NasaCatalog $nasaCatalog)
    {
        $this->checkAccess($nasaCatalog);

        $form = $this->createForm(NasaCatalogType::class, $nasaCatalog, [
            'action' => $this->generateUrl('nasa_save', ['id' => $nasaCatalog->getId()])
        ]);

        return $form;
    }

    /**
     * @param $context
     * @param $latitude
     * @param $longitude
     * @return NasaCatalog|NasaCatalogInterface|null
     */
    private function loadOrCreate($context, $latitude, $longitude)
    {
        $nasaCatalog = $this->getNasaInfo($context, $latitude, $longitude);

        if (!$nasaCatalog->getAccount() || $nasaCatalog->getAccount()->getId() != $this->getCurrentAccount()->getId()) {

            $catalog = new NasaCatalog();
            $catalog
                ->setAccount($this->getCurrentAccount())
                ->setContext($nasaCatalog->getContext())
                ->setMonths($nasaCatalog->getMonths())
                ->setLatitude($nasaCatalog->getLatitude())
                ->setLongitude($nasaCatalog->getLongitude());

            $this->getNasaProvider()->save($catalog);

            return $catalog;
        }

        return $nasaCatalog;
    }

    /**
     * @param $context
     * @param $latitude
     * @param $longitude
     * @return null|NasaCatalogInterface
     */
    private function getNasaInfo($context, $latitude, $longitude)
    {
        $criteria = [
            'context' => $context,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'account' => $this->getCurrentAccount()
        ];

        $provider = $this->getNasaProvider();

        $info = $provider->findOneBy($criteria);

        if (!$info instanceof NasaCatalogInterface) {
            unset($criteria['account']);
            $info = $provider->findOneBy($criteria);
        }

        return $info;
    }

    /**
     * @param NasaCatalog $nasaCatalog
     */
    private function checkAccess(NasaCatalog $nasaCatalog)
    {
        if (!$nasaCatalog->getAccount() || $nasaCatalog->getAccount()->getId() != $this->getCurrentAccount()->getId()) {
            $this->createAccessDeniedException();
        }
    }
}