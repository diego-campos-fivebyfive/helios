<?php

namespace AppBundle\Form\Project;

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Component\KitComponentInterface;
use AppBundle\Entity\Project\MpptOperation;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\Project\ProjectInverter;
use AppBundle\Entity\Project\ProjectInverterInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectInverterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProjectInverterInterface $projectInverter */
        $projectInverter = $options['data'];
        $project = $projectInverter->getProject();
        $kit = $project->getKit();
        $kitInverter = $projectInverter->getInverter();
        $inverter = $kitInverter->getInverter();
        $mppt = $inverter->getMpptNumber();

        $kitInverters = $kit->getInverters();

        //dump($inverter); die;

        $builder
            ->add('inverter', EntityType::class, [
                'class' => KitComponent::class,
                'choices' => $kitInverters,
                'multiple' => false
            ])
            ->add('operation', EntityType::class, [
                'placeholder' => 'select.operation',
                'class' => MpptOperation::class,
                'query_builder' => function(EntityRepository $er) use($mppt) {
                    return $er->createQueryBuilder('o')
                        ->where('o.mppt = :mppt')
                        ->setParameter('mppt', $mppt);
                }
            ])
            ->add('loss')
        ;

        /*$builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use($kitInverters){

            $form = $event->getForm();
            $data = $event->getData();

            if(array_key_exists('inverter', $data)){

                $inverterId = (int) $data['inverter'];

                $kitInverter = $kitInverters->filter(function(KitComponentInterface $kitInverter) use($inverterId){
                    return $inverterId == $kitInverter->getId();
                })->first();

                if(!$kitInverter instanceof KitComponentInterface)
                    throw new \InvalidArgumentException('Invalid Kit Inverter Reference');

                $inverter = $kitInverter->getInverter();

                if(!$inverter instanceof InverterInterface)
                    throw new \InvalidArgumentException('Invalid Inverter Reference');

                $mppt = $inverter->getMpptNumber();

                $this->addOperationField($form, $mppt);
            }

            //dump($form); die;
        });*/

        /*
            ->add('operation', EntityType::class, [
                'class' => MpptOperation::class,
                'query_builder' => function(EntityRepository $er) use($builder, $project){

                    $key = (int) $builder->getName();
                    /** @var ProjectInverter $projectInverter *
                    $projectInverter = $project->getInverters()->get($key);
                    $kitInverter = $projectInverter->getInverter();
                    $inverter = $kitInverter->getInverter();

                    return $er->createQueryBuilder('o')
                        ->where('o.mppt = :mppt')
                        ->setParameter('mppt', $inverter->getMpptNumber())
                        ;
                }
            ])
            ->add('modules', CollectionType::class, [
                'entry_type' => ProjectModuleType::class,
                'entry_options' => [
                    'project' => $project
                ],
                'allow_add' => false
            ])
        ;
        //dump($builder); die;
        */
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project\ProjectInverter',
            'project' => null
        ));
    }

    /**
     * @param FormInterface $form
     * @param $mppt
     */
    private function addOperationField(FormInterface &$form, $mppt)
    {
        $form->add('operation', EntityType::class, [
            'class' => MpptOperation::class,
            'query_builder' => function(EntityRepository $er) use($mppt) {
                return $er->createQueryBuilder('o')
                    ->where('o.mppt = :mppt')
                    ->setParameter('mppt', $mppt);
            }
        ]);
    }
}
