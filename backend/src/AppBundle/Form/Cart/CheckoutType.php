<?php

namespace AppBundle\Form\Cart;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('neighborhood', TextType::class, [
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
            ->add('shippingName', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingEmail', EmailType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingPhone', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingPostcode', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingState', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingCity', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingNeighborhood', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingStreet', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingNumber', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shippingComplement', TextType::class, [
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
