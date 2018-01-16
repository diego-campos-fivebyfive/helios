<?php

namespace AdminBundle\Form\Misc;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description');
        $builder->add('amount');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array( ));
    }
}
