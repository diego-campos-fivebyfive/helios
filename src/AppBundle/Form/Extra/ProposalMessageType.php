<?php

namespace AppBundle\Form\Extra;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProposalMessageType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class)
            ->add('to', EmailType::class)
            //->add('replyTo', EmailType::class)
            ->add('body', TextareaType::class, [
                'required' => false
            ])
            ->add('sendCopy', CheckboxType::class, [
                'required' => false
            ])
        ;
    }
}