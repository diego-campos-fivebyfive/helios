<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\MakerInterface;
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
        $builder->add("country");
        $builder->add('enabled', ChoiceType::class, [
            'choices' => [
                MakerInterface::STATUS_ENABLED => "Enabled",
                MakerInterface::STATUS_DISABLED => "Disabled",
            ],
                ]
        );
        $builder->add('context', ChoiceType::class, [
            'choices' => [
                MakerInterface::CONTEXT_MODULE => "Fabricante de Módulos",
                MakerInterface::CONTEXT_INVERTER => "Fabricante de Inversores"
                //MakerInterface::CONTEXT_ALL => "Ambos",
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
