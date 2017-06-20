<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\FormBuilderInterface;

class Helper
{
    public static function addMetadataType(FormBuilderInterface &$builder)
    {
        $builder->add($builder->create('metadata', 'form')
            ->add('image_link', 'text', [
                'required' => false
            ])
            ->add('page_link', 'text', [
                'required' => false
            ])
            ->add('datasheet_link', 'text', [
                'required' => false
            ])
        );
    }
}