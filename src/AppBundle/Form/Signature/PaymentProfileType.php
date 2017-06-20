<?php

namespace AppBundle\Form\Signature;

use AppBundle\Model\Signature\PaymentProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentProfileType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('holder_name', TextType::class, [
                'required' => true
            ])
            ->add('card_expiration', TextType::class, [
                'required' => true
            ])
            ->add('card_number', TextType::class, [
                'required' => true
            ])
            ->add('card_cvv', TextType::class, [
                'required' => true
            ]);

        $builder->add('terms', CheckboxType::class);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentProfile::class
        ]);
    }
}