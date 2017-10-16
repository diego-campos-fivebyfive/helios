<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                ->add(
                    $builder
                        ->create('platform', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add(
                    $builder
                        ->create('master', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add(
                    $builder
                        ->create('admin', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )->add(
                    $builder
                        ->create('financial', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add('enable_promo', CheckboxType::class)
                ->add('promo_end_at', TextType::class)
        );
        $builder->get('parameters')
            ->get('promo_end_at')
            ->addModelTransformer(new CallbackTransformer(
                function ($endAt) {
                    return (new \DateTime($endAt['date']))->format('d/m/Y');
                },
                function ($endAt) {
                    return new \DateTime(implode('-', array_reverse(explode('/', $endAt))));
                }
            ));
    }
}
