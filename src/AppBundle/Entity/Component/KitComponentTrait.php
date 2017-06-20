<?php

namespace AppBundle\Entity\Component;

trait KitComponentTrait
{
    public function getMakerName()
    {
        return $this->getComponent()->getMaker();
    }

    public function getModel()
    {
        return $this->getComponent()->getModel();
    }

    public function toArray()
    {
        /** @var ComponentInterface $component */
        $component = $this->getComponent();

        $data = [
            'reference_id' => $this->getId(),
            'quantity' => $this->quantity,
            'price' => (float)$this->price
        ];

        return array_merge($component->toArray(), $data);
    }
}