<?php

namespace AppBundle\Form\Financial;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinancialType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lifetime', TextType::class, [
                'required' => true
            ])
            ->add('inflation', null, [
                'required' => false
            ])
            ->add('efficiencyLoss', null, [
                'required' => false
            ])
            ->add('annualCostOperation', MoneyType::class, [
                'currency' => false,
                'required' => false
            ])
            ->add('energyPrice', MoneyType::class, [
                'currency' => false,
                'required' => false
            ])
        ;
    }
}