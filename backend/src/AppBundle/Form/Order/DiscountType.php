<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Order\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $member = $options['member'];

        $choices = [
            Order::DISCOUNT_PERCENT => 'Desconto Percentual',
            Order::DISCOUNT_FIXED => 'Desconto Fixo'
        ];

        if ($member->isPlatformExpanse())
            unset($choices[Order::DISCOUNT_FIXED]);

        $builder->add('target', ChoiceType::class, [
            'choices' => $choices
            ]);
        $builder->add('value', TextType::class, [
            'required' => false
        ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'member' => null,
            'discount' => null
        ));
    }
}
