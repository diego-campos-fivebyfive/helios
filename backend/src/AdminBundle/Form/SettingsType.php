<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('parameters', FormType::class)
                ->add('email_platform', EmailType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Email da Plataforma'
                    ]
                ])
                ->add('email_master', EmailType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Email Master'
                    ]
                ])
                ->add('email_admin', EmailType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Email Administrador'
                    ]
                ])
        );
    }
}
