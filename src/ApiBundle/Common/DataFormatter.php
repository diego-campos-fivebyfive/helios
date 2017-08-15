<?php

namespace ApiBundle\Common;

use JMS\Serializer\SerializerInterface;

class DataFormatter
{
    /**
     * @var array
     */
    private $converts = [];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $converts
     */
    public function setConverts(array $converts)
    {
        $this->converts = $converts;
    }

    /**
     * @param $data
     */
    public function format($data, array $converts = [])
    {
        $converts = $converts ?: $this->converts;
        $formatted = [];

        if (is_array($data)) {

            $keys = array_keys($data);
            if (is_int($keys[0])) {
                foreach ($data as $item) {
                    $formatted[] = $this->format($item, $converts);
                }
            }

        }else {

            $formatted = $this->toArray($data);
            foreach ($converts as $field => $target) {
                if (array_key_exists($field, $formatted)) {
                    $formatted[sprintf('%s_%s', $field, $target)] = $formatted[$field][$target];
                    unset($formatted[$field]);
                }
            }
        }

        return $formatted;
    }

    /**
     * @param object|array $data
     * @return string
     */
    public function toJson($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

    /**
     * @param object|string|array $data
     * @return array
     */
    public function toArray($data)
    {
        return json_decode($this->toJson($data), true);
    }
}