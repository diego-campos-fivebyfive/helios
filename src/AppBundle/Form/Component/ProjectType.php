<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Component\ProjectInterface $project */
        $project = $options['data'];

        $builder
            ->add('customer')
            ->add('address')
            ->add('latitude')
            ->add('longitude')
            ->add('infConsumption')
            ->add('roofType', ChoiceType::class, [
                'choices' => Project::getRootTypes()
            ])
            ->add('structureType', ChoiceType::class, [
                'choices' => Project::getStructureTypes()
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }
}