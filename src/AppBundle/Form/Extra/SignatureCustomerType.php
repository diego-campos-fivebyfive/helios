<?php

namespace AppBundle\Form\Extra;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SignatureCustomerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('registry_code')
            ->add(
                $builder->create('address', FormType::class)
                    ->add('zipcode')
                    ->add('street')
                    ->add('number')
                    ->add('neighborhood')
                    ->add('additional_details', TextType::class, [
                        'required' => false
                    ])
                    ->add('city', TextType::class, [
                        'attr' => [
                            'readonly' => true
                        ]
                    ])
                    ->add('state', TextType::class, [
                        'attr' => [
                            'readonly' => true
                        ]
                    ])
                    ->add('country')
            );
    }
}