<?php

namespace ApiBundle\Model\Error;

use Symfony\Component\Form\FormInterface;

/**
 * Class Normalizer
 */
class Normalizer
{
    /**
     * @param FormInterface $form
     * @return array
     */
    public static function normalize(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error){
            $payload = $error->getCause()->getConstraint()->payload;
            $errors[] = (new Error(
                $payload,
                'invalid_parameter',
                $error->getMessage()
            ))->toArray();
        }

        return $errors;
    }
}