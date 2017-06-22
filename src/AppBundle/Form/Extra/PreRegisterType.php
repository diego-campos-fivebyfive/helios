<?php
/**
 * Created by PhpStorm.
 * User: Fabio
 * Date: 22/06/2017
 * Time: 11:15
 */

namespace AppBundle\Form\Extra;

use AppBundle\Entity\Extra\AccountRegister;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PreRegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('document')
            ->add('inscription')
            ->add('lastname')
            ->add('firstname')
            ->add('contact')
            ->add('email',EmailType::class)
            ->add('phone')

            ->add('postcode')
            ->add('state')
            ->add('city')
            ->add('district')
            ->add('street')
            ->add('number');
        
    }

}