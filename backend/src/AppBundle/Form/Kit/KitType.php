<?php

namespace AppBundle\Form\Kit;

use AppBundle\Entity\Kit\Kit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true
            ])
            ->add('description', TextType::class, [
                'required' => true
            ])
            ->add('power', MoneyType::class, [
                'currency' => false,
                'scale' => 2,
                'required' => true
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'scale' => 2,
                'required' => true
            ])
            ->add('stock', TextType::class, [
                'required' => true
            ])
            ->add('position', TextType::class, [
                'required' => true
            ])
            ->add('available', CheckboxType::class, [
                'required' => false
            ])
            ->add('components', null, [
                'mapped' => false,
                'required' => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
