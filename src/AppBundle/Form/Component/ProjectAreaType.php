<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectModule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectAreaType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProjectArea $projectArea */
        $projectArea = $options['data'];
        $projectInverter = $projectArea->getProjectInverter();
        $project = $projectInverter->getProject();
        $projectModules = $project->getProjectModules();

        $builder
            ->add('projectModule', EntityType::class, [
                'placeholder' => 'select.module',
                'class' => ProjectModule::class,
                'choices' => $projectModules,
                'choice_label' => function(ProjectModule $projectModule){
                    //return $module->getModule()->getMaker()->getName() . ' / ' . $module->getModule()->getModel();
                    return $projectModule->getModule()->getModel();
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
            'data_class' => ProjectArea::class
        ));
    }
}