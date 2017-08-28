<?php

namespace AppBundle\Form\Generator;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class GeneratorType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('power')
            ->add('module', EntityType::class, [
                'class' => Module::class
            ])
            ->add('inverter_maker', EntityType::class, [
                'class' => Maker::class,
                'query_builder' => function(EntityRepository $er){

                    return $er->createQueryBuilder('m')
                        ->join(Inverter::class, 'i', 'WITH', 'i.maker = m.id')
                        ->where('m.context = :context')
                        ->setParameters([
                            'context' => 'component_inverter'
                        ]);
                },
            ])
            ->add('roof', ChoiceType::class, [
                'choices' => Project::getRoofTypes()
            ])
            ->add('structure_maker', ChoiceType::class, [
                'choices' => Project::getStructureTypes()
            ])
            ->add('structure_maker', ChoiceType::class, [
                'choices' => Project::getStructureTypes()
            ])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    0 => 'Vertical',
                    1 => 'Horizontal'
                ]
            ])
        ;
    }
}