<?php

namespace AppBundle\Form\Financial;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CompanyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['required' => false]);
        $builder->add('contact', null, ['required' => false]);
        $builder->add('phone', null, ['required' => false]);
        $builder->add('email', EmailType::class, ['required' => false]);
        $builder->add('address', null, ['required' => false]);
        $builder->add('postcode', null, ['required' => false]);
        $builder->add('city', null, ['required' => false]);
        $builder->add('state', null, ['required' => false]);
    }
}
