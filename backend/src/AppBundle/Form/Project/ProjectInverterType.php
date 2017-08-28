<?php

namespace AppBundle\Form\Project;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectInverterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter */
        $projectInverter = $options['data'];
        /** @var \AppBundle\Manager\MpptManager $mpptManager */
        $mpptManager = $options['mppt_manager'];
        $mppt = $projectInverter->getInverter()->getMpptNumber();

        $builder
            /*->add('inverter', EntityType::class, [
                'class' => Inverter::class,
                //'choices' => $kitInverters,
                'multiple' => false
            ])*/
            ->add('operation', ChoiceType::class, [
                'placeholder' => 'select.operation',
                'choices' => $mpptManager->getChoices($mppt)
            ])
            ->add('loss')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \AppBundle\Entity\Component\ProjectInverter::class,
            'project' => null
        ));

        $resolver->setRequired('mppt_manager');
    }
}
