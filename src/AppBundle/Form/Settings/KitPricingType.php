<?php

namespace AppBundle\Form\Settings;

use AppBundle\Model\KitPricing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class KitPricingType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('target', ChoiceType::class, [
                'choices' => KitPricing::getTargets()
            ])
            ->add('percent', null, [
                'required' => true
            ])
        ;
    }
}