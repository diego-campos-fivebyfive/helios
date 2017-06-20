<?php

namespace AppBundle\Form;

use AppBundle\Entity\Customer;
use Kolina\CustomerBundle\Form\CustomerType as AbstractCustomerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractCustomerType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('user');

        $builder
            ->add('package', 'entity', array(
                    'multiple' => false,
                    'property' => 'name',
                    'class' => 'AppBundle\Entity\Package',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('p');
                        return $qb->orderBy('p.name', 'ASC');
                    }
                )
            )
            ->add('status', ChoiceType::class, [
                'choices' => Customer::getStatusList()
            ])
            ->add('members', CollectionType::class, [
                'entry_type' => CustomerType::class,
                'allow_add' => true,
                'prototype_name' => 0
            ])
            ->add('maxMember');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Customer'
        ));
    }

}
