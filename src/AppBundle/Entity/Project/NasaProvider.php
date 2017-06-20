<?php

namespace AppBundle\Entity\Project;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NasaProvider extends BaseEntityManager implements NasaProviderInterface
{
    //use NasaBadMethod;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @inheritDoc
     */
    public function __construct($class, ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($class, $registry);

        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritDoc
     */
    public function fromCoordinates($latitude, $longitude)
    {
        $latitude = floor($latitude);
        $longitude = floor($longitude);

        $radiationGlobal = $this->radiationGlobal($latitude, $longitude);
        $radiationDiffuse = $this->radiationDiffuse($latitude, $longitude);
        $radiationAtmosphere = $this->radiationAtmosphere($latitude, $longitude);
        $airTemperature = $this->airTemperature($latitude, $longitude);
        $airTemperatureMin = $this->airTemperatureMin($latitude, $longitude);
        $airTemperatureMax = $this->airTemperatureMax($latitude, $longitude);
        $solarNoon = $this->solarNoon($longitude);
        $daylightHours = $this->daylightHours($latitude);

        return [
            NasaCatalog::RADIATION_GLOBAL => $radiationGlobal,
            NasaCatalog::RADIATION_DIFFUSE => $radiationDiffuse,
            NasaCatalog::RADIATION_ATMOSPHERE => $radiationAtmosphere,
            NasaCatalog::AIR_TEMPERATURE => $airTemperature,
            NasaCatalog::AIR_TEMPERATURE_MIN => $airTemperatureMin,
            NasaCatalog::AIR_TEMPERATURE_MAX => $airTemperatureMax,
            NasaCatalog::SOLAR_NOON => $solarNoon,
            NasaCatalog::DAYLIGHT_HOURS => $daylightHours
        ];

        /*
        $result = parent::findBy([
            'latitude' => floor($latitude),
            'longitude' => floor($longitude)
        ]);

        if(!empty($result)){

            $data = [];
            foreach($result as $catalog){
                if($catalog instanceof NasaCatalogInterface) {
                    $data[$catalog->getContext()] = $catalog->toArray();
                }
            }

            $solarNoon = $this->solarNoon($longitude);
            if($solarNoon){
                $data[NasaCatalogInterface::SOLAR_NOON] = $solarNoon;
            }

            $daylightHours = $this->daylightHours($latitude);
            if($daylightHours){
                $data[NasaCatalogInterface::DAYLIGHT_HOURS] = $daylightHours;
            }

            return $data;
        }

        return null;
        */
    }

    /**
     * @inheritDoc
     */
    public function radiationGlobal($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::RADIATION_GLOBAL, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function radiationDiffuse($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::RADIATION_DIFFUSE, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function radiationAtmosphere($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::RADIATION_ATMOSPHERE, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function airTemperature($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::AIR_TEMPERATURE, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function airTemperatureMin($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::AIR_TEMPERATURE_MIN, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function airTemperatureMax($latitude, $longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::AIR_TEMPERATURE_MAX, $latitude, $longitude);
    }

    /**
     * @inheritDoc
     */
    public function daylightHours($latitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::DAYLIGHT_HOURS, $latitude, null);
    }

    /**
     * @inheritDoc
     */
    public function solarNoon($longitude)
    {
        return $this->findByCoordinates(NasaCatalogInterface::SOLAR_NOON, null, $longitude);
    }

    /**
     * @param $context
     * @param $latitude
     * @param $longitude
     * @return array|null
     */
    private function findByCoordinates($context, $latitude = null, $longitude = null)
    {
        $account = $this->account();

        $nasaCatalog = parent::findOneBy([
            'context' => $context,
            'latitude' => $latitude ? floor($latitude) : $latitude,
            'longitude' => $longitude ? floor($longitude) : $longitude,
            'account' => $account
        ]);

        if(!$nasaCatalog){
            $nasaCatalog = parent::findOneBy([
                'context' => $context,
                'latitude' => $latitude ? floor($latitude) : $latitude,
                'longitude' => $longitude ? floor($longitude) : $longitude
            ]);
        }

        if($nasaCatalog instanceof NasaCatalogInterface){
            return $nasaCatalog->toArray();
        }

        return null;
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface
     */
    private function account()
    {
        /** @var \AppBundle\Entity\UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        return $user->getInfo()->getAccount();
    }
}