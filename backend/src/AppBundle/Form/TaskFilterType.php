<?php

namespace AppBundle\Form;

use AppBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskFilterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => Task::getTypes(),
                'expanded' => true,
                'multiple' => true
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'choices' => Task::getStatuses(),
                'multiple' => false,
                'expanded' => true
            ])
            ->add('date', ChoiceType::class, [
                'required' => false,
                'choices' => Task::getFilterChoices(),
                'expanded' => true,
                'multiple' => false
            ])
            ->add('contact', TextType::class, [
                'required' => false
            ])
        ;
    }

}