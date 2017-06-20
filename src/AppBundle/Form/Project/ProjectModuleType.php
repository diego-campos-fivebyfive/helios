<?php

namespace AppBundle\Form\Project;

use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\Project\ProjectModuleInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectModuleType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProjectModuleInterface $projectModule */
        $projectModule = $options['data'];
        $projectInverter = $projectModule->getInverter();
        $project = $projectInverter->getProject();
        $kit = $project->getKit();
        $kitModules = $kit->getModules();

        $builder
            ->add('module', EntityType::class, [
                'placeholder' => 'select.module',
                'class' => KitComponent::class,
                'choices' => $kitModules,
                'choice_label' => function(KitComponent $module){
                    return $module->getModule()->getMaker()->getName() . ' / ' . $module->getModule()->getModel();
                }
            ])
            ->add('inclination', TextType::class)
            ->add('orientation', TextType::class)
            ->add('stringNumber', TextType::class)
            ->add('moduleString', TextType::class)
            ->add('loss')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project\ProjectModule',
            'project' => null
        ));
    }
}