<?php

namespace AppBundle\Service\SolarEnergy\Nasa;

abstract class NasaYear implements NasaYearInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return !count($this->data);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        if(count($data) != self::LENGTH_DATA)
            $this->invalidLengthException(count($data));

        $this->normalizeData($data);

        $this->data = $data;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    private function normalizeData(array &$data)
    {
        $data = array_combine(array_keys(array_fill(1, 12, null)), $data);

        foreach($data as $key => $value) {
            $data[$key] = (float)$value;
        }
    }

    /**
     * @return array
     */
    public static function getSolarDeclinations()
    {
        return [
            deg2rad(-20.9), deg2rad(-13), deg2rad(-2.4), deg2rad(9.4), deg2rad(18.8), deg2rad(23.1),
            deg2rad(21.2), deg2rad(13.5), deg2rad(2.2), deg2rad(-9.6), deg2rad(-18.9), deg2rad(-23)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSoloReflectance()
    {
        return self::SOLO_REFLECTANCE;
    }

    /**
     * @param $current
     * @throws \Exception
     */
    private function invalidLengthException($current)
    {
        throw new \Exception(sprintf('Invalid length data. current: %s - expected: %s', $current, self::LENGTH_DATA));
    }
}