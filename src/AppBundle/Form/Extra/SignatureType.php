<?php

namespace AppBundle\Form\Extra;

use AppBundle\Model\Signature\Signature;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignatureType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plan_id')
            ->add('customer_id')
            ->add('payment_method_code')
            ->add(
                $builder->create('payment_profile', FormType::class)
                    ->add('holder_name')
                    ->add('card_expiration')
                    ->add('card_number')
                    ->add('card_cvv')
            )
            ->add('product_items', CollectionType::class, [
                'entry_type' => SignatureItemType::class
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Signature::class
        ]);
    }
}