<?php

namespace AppBundle\Form\Cart;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('document', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('postcode', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('state', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('neighbourhood', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('street', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('number', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('complement', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentDelivery', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
            ]);

        $builder
            ->add('differentName', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentEmail', EmailType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentPhone', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentPostcode', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentState', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentCity', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentNeighbourhood', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentStreet', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentNumber', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('differentComplement', TextType::class, [
                'mapped' => false,
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
