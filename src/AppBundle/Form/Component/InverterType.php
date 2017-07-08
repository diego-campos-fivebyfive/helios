<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Component\MakerInterface;

class InverterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'maker', 'entity', array(
                'required' => true,
                'multiple' => false,
                'property' => 'name',
                'class' => 'AppBundle\Entity\Component\Maker',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er){

                    $parameters = ['context' => MakerInterface::CONTEXT_INVERTER];

                    $qb = $er
                        ->createQueryBuilder('m')
                        ->where('m.context = :context')
                        ->orderBy('m.name', 'ASC');

                    $qb->setParameters($parameters);

                    return $qb;
                }
            )
        );

        $builder->add('model', TextType::class, [
            'required' => true
        ]);

        $builder->add('nominalPower', null, [
            'required' => true
        ]);
        $builder->add('maxDcPower', null, [
            'required' => true
        ]);
        $builder->add('maxDcVoltage', null, [
            'required' => true
        ]);
        $builder->add('mpptMaxDcCurrent', null, [
            'required' => true
        ]);
        $builder->add('maxEfficiency', null, [
            'required' => true
        ]);
        $builder->add('mpptMax', null, [
            'required' => true
        ]);
        $builder->add('mpptMin', null, [
            'required' => true
        ]);
        $builder->add('mpptNumber', null, [
            'required' => true
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Inverter',
            'is_validation' => false
        ));
    }

}
