<?php

namespace AdminBundle\Form\PaymentMethod;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentQuotaType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('percent')
            ->add('days')
            ->add('display_payment_date', CheckboxType::class,[
                'required' => false
            ])
        ;
    }
}
