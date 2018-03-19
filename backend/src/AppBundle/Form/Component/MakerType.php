<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\MakerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Entity\Component\Maker;

class MakerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name");
        $builder->add('enabled', CheckboxType::class, [
            'enabled' => MakerInterface::STATUS_ENABLED,
            'disabled' => MakerInterface::STATUS_DISABLED
        ]);
        $builder->add('context', ChoiceType::class, [
            'choices' => [
                MakerInterface::CONTEXT_MODULE => "Fabricante de Módulos",
                MakerInterface::CONTEXT_INVERTER => "Fabricante de Inversores",
                MakerInterface::CONTEXT_STRING_BOX => "Fabricande de String Box",
                MakerInterface::CONTEXT_VARIETY => "Fabricante de Variedades",
                MakerInterface::CONTEXT_STRUCTURE => "Fabricante de Estruturas"
            ],
                ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Maker'
        ));
    }

}
