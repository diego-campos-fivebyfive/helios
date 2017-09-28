<?php

namespace AdminBundle\Form\PaymentMethod;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentMethodType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('quotas', CollectionType::class, [
                'entry_type' => PaymentQuotaType::class,
                'allow_add' => true,
                'entry_options' => [
                    'label' => false
                ]
            ])
        ;
    }
}
