<?php

namespace ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class ComponentType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true
            ])
            ->add('model', TextType::class, [
                'required' => true
            ])
            ->add('available', BooleanType::class, [
                'required' => true
            ])
            ->add('status', BooleanType::class, [
                'required' => true
            ])
        ;
    }
}