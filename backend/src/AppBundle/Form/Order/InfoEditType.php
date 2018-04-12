<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoEditType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Billing
        $builder
            ->add('billingDirect', CheckboxType::class, [
                'required' => false
            ])
            ->add('billingFirstname', TextType::class, [
                'required' => false
            ])
            ->add('billingLastname', TextType::class, [
                'required' => false
            ])
            ->add('billingContact', TextType::class, [
                'required' => false
            ])
            ->add('billingCnpj', TextType::class, [
                'required' => false,
            ])
            ->add('billingIe', TextType::class, [
                'required' => false
            ])
            ->add('billingPhone', TextType::class, [
                'required' => false
            ])
            ->add('billingEmail', TextType::class, [
                'required' => false
            ])
            ->add('billingPostcode', TextType::class, [
                'required' => false
            ])
            ->add('billingCity', TextType::class, [
                'required' => false
            ])
            ->add('billingState', TextType::class, [
                'required' => false
            ])
            ->add('billingDistrict', TextType::class, [
                'required' => false
            ])
            ->add('billingStreet', TextType::class, [
                'required' => false
            ])
            ->add('billingNumber', TextType::class, [
                'required' => false
            ])
            ->add('billingComplement', TextType::class, [
                'required' => false
            ])
            ->add('antecipatedBilling', CheckboxType::class, [
                'required' => false
            ]);

        // Payment and Delivery
        $builder
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => $options['paymentMethods']
            ])
            ->add('deliveryAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('deadline')
            ->add('note')
            ->add('deliveryDelay')
            ->add('expireDays');

        // Address
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
            ]);

        // ERP
        $builder
            ->add('erpOR', TextType::class, [
                'required' => false
            ])
            ->add('erpOP', TextType::class, [
                'required' => false
            ])
            ->add('erpPV', TextType::class, [
                'required' => false
            ])
            ->add('erpRPV', TextType::class, [
                'required' => false
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Order::class,
            'paymentMethods' => []
        ]);
    }
}
