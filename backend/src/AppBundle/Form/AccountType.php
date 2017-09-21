<?php

namespace AppBundle\Form;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('user');

        $builder
            ->add('document',TextType::class, array(
                'constraints' => new ContainsCnpj()
            ))
            ->add('extraDocument',TextType::class, [
                'required' => false
            ])
            ->add('lastname',TextType::class)
            ->add('firstname',TextType::class)
            ->add('postcode',TextType::class)
            ->add('state',TextType::class)
            ->add('city',TextType::class)
            ->add('district',TextType::class)
            ->add('street',TextType::class)
            ->add('number',TextType::class, [
                'required' => false
            ])
            ->add('level', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels()
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Customer::getStatusList()
            ])
            ->add('contact',TextType::class)
            ->add('email',EmailType::class)
            ->add('phone',TextType::class);
        /*$builder
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
            ->add('maxMember');*/
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
