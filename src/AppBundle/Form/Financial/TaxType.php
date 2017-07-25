<?php

namespace AppBundle\Form\Financial;

use AppBundle\Entity\Component\ProjectTax;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operation', ChoiceType::class, [
                'choices' => ProjectTax::getTaxOperations()
            ])
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('type', ChoiceType::class, [
                'choices' => ProjectTax::getTaxTypes()
            ])
            ->add('target', ChoiceType::class, [
                'choices' => ProjectTax::getTaxTargets()
            ])
            ->add('value', MoneyType::class, [
                'currency' => false,
                'required' => true
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProjectTax::class
        ));
    }
}