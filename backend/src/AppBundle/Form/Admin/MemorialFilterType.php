<?php

namespace AppBundle\Form\Admin;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MemorialFilterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Memorial $memorial */
        $levels = $options['levels'];

        $builder
            ->add('memorial', EntityType::class, [
                'placeholder' => 'Selecionar Memorial',
                'class' => Memorial::class
            ])
            ->add('level', ChoiceType::class, [
                'choices' => array_combine($levels, $levels)
            ])
            ->add('components', ChoiceType::class, [
                'choices' => [
                    'module' => 'Módulos',
                    'inverter' => 'Inversores',
                    'string_box' => 'String Box',
                    'structure' => 'Estrutura',
                    'variety' => 'Variedades'
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('memorial')
            ->setDefaults([
                'method' => 'get',
                'csrf_protection' => false,
                'levels' => [],
                'memorials' => []
            ])
        ;
    }
}
