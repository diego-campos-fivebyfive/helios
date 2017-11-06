<?php

namespace AppBundle\Form\Extra;

use AppBundle\Configuration\Brazil;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PreRegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ->add('state',ChoiceType::class, [
                'choices' => Brazil::states()
            ])
            ->add('city',TextType::class)
            ->add('district',TextType::class)
            ->add('street',TextType::class)
            ->add('number',TextType::class, [
                'required' => false
            ])

            ->add('contact',TextType::class)
            ->add('email',EmailType::class)
            ->add('phone',TextType::class);
        
    }

}
