<?php

namespace AppBundle\Form\Kit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitType extends AbstractType
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
            ->add('description', TextType::class, [
                'required' => true
            ])
            ->add('power', TextType::class, [
                'required' => true
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'scale' => 2,
                'required' => true
            ])
            ->add('stock', TextType::class, [
                'required' => true
            ])
            ->add('image', TextType::class, [
                'required' => false
            ])
            ->add('position', TextType::class, [
                'required' => true
            ])
            ->add('available', CheckboxType::class, [
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
