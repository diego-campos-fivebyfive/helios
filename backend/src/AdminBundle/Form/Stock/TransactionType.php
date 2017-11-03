<?php

namespace AdminBundle\Form\Stock;

use AppBundle\Entity\Stock\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description');
        $builder->add('amount');
        $builder->add('mode', ChoiceType::class, [
            'choices' => [
                '1' => 'credit',
                '-1' => 'debit'
            ]
        ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array( ));
    }
}
