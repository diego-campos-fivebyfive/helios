<?php

namespace AppBundle\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DocSectionType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'required' => false,
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('editable', CheckboxType::class, [
                'required' => false,
                'label' => 'proposal.message_editable_section',
            ])
            ->add('content', TextareaType::class,[
                'required' => false,
                'attr' => [
                    'placeholder' => 'Content'
                ]
            ])
            ->add('order', HiddenType::class,[
                'attr' => [
                    'class' => 'input-order'
                ]
            ])
        ;
    }
}