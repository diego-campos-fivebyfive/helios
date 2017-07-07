<?php

namespace AppBundle\Manager;

class MpptManager extends AbstractManager
{
    /**
     * @param $mppt
     * @return array
     */
    public function getChoices($mppt)
    {
        $operations = $this->findBy([
            'mppt' => $mppt
        ]);

        $choices = [];

        /** @var \AppBundle\Entity\Project\MpptOperation $operation */
        foreach ($operations as $operation){
            $choices[$operation->getId()] = $operation->getName();
        }

        return $choices;
    }
}