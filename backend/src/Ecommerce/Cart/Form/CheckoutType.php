<?php

namespace Ecommerce\Cart\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $required = [
            'mapped' => false,
            'required' => true
        ];

        $notRequired = [
            'mapped' => false,
            'required' => false
        ];

        $builder
            ->add('firstName', TextType::class, $required)
            ->add('lastName', TextType::class, $required)
            ->add('documentType', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'choices' => [
                    'CNPJ' => 'CNPJ',
                    'CPF' => 'CPF'
                ]
            ])
            ->add('document', TextType::class, $required)
            ->add('email', EmailType::class, $required)
            ->add('phone', TextType::class, $required)
            ->add('postcode', TextType::class, $required)
            ->add('state', TextType::class, $required)
            ->add('city', TextType::class, $required)
            ->add('neighborhood', TextType::class, $notRequired)
            ->add('street', TextType::class, $required)
            ->add('number', TextType::class, $required)
            ->add('complement', TextType::class, $notRequired)
            ->add('differentDelivery', CheckboxType::class, $notRequired);

        $builder
            ->add('shippingFirstName', TextType::class, $notRequired)
            ->add('shippingLastName', TextType::class, $notRequired)
            ->add('shippingEmail', EmailType::class, $notRequired)
            ->add('shippingPhone', TextType::class, $notRequired)
            ->add('shippingPostcode', TextType::class, $notRequired)
            ->add('shippingState', TextType::class, $notRequired)
            ->add('shippingCity', TextType::class, $notRequired)
            ->add('shippingNeighborhood', TextType::class, $notRequired)
            ->add('shippingStreet', TextType::class, $notRequired)
            ->add('shippingNumber', TextType::class, $notRequired)
            ->add('shippingComplement', TextType::class, $notRequired);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
