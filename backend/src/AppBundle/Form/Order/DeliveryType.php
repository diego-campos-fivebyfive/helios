<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Order\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryPostcode', null, [
                'required' => true
            ])
            ->add('deliveryState', null, [
                'required' => true
            ])
            ->add('deliveryCity', null, [
                'required' => true
            ])
            ->add('deliveryDistrict', null, [
                'required' => true
            ])
            ->add('deliveryStreet', null, [
                'required' => true
            ])
            ->add('deliveryNumber', null, [
                'required' => true
            ])
            ->add('deliveryComplement', null, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Order::class
            ]);
    }
}
