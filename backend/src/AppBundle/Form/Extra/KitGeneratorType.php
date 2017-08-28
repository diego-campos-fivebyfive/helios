<?php

namespace AppBundle\Form\Extra;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class KitGeneratorType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tiposTelhado = [
            'Telhas Romanas e Americanas',
            'Ficrocimento',
            'Laje Plana',
            'Chapa Metálica',
            'Chapa Metálica com Perfil de 0,5m'
        ];

        $makers = ['SICES Solar', 'K2 System'];

        $builder
            ->add('kwh')
            ->add('tipo_telhado', ChoiceType::class, [
                'choices' => array_combine($tiposTelhado, $tiposTelhado)
            ])
            ->add('maker_est', ChoiceType::class, [
                'choices' => array_combine($makers, $makers)
            ])
            ->add('latitude')
            ->add('longitude')
        ;
    }
}