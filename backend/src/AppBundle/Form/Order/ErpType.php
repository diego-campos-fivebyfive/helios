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

class ErpType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            'class' => Order::class
        ]);
    }
}