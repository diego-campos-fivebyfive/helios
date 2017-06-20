<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\InverterInterface;

class InverterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Component\ComponentInterface $component */
        $component = $options['data'];

        $account = $component->getAccount();
        $makerId = $options['is_validation'] ? $component->getMaker()->getId() : null;

        $builder->add(
            'maker', 'entity', array(
                'required' => true,
                'multiple' => false,
                'property' => 'name',
                'class' => 'AppBundle\Entity\Component\Maker',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($account, $makerId) {

                    $parameters = ['context' => MakerInterface::CONTEXT_INVERTER];

                    $qb = $er
                        ->createQueryBuilder('m')
                        ->where('m.context = :context')
                        ->orderBy('m.name', 'ASC');

                    if (!$makerId) {

                        $qb->andWhere(
                            $qb->expr()->orX(
                                'm.account is null',
                                'm.account = :account'
                            )
                        );

                        $parameters['account'] = $account;

                    } else {

                        $qb->andWhere('m.id = :id');

                        $parameters['id'] = $makerId;
                    }

                    $qb->setParameters($parameters);

                    return $qb;
                }
            )
        );

        //$builder->add('type');
        //$builder->add('serial');
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

        //Helper::addMetadataType($builder);
        /*
        $builder->add('country');
        $builder->add('warranty');
        $builder->add('nominalDcVoltage');
        $builder->add('voltageStartFeed');
        $builder->add('nominalDcCurrent');
        $builder->add('maxAcPower');
        $builder->add('nominalAcVoltage');
        $builder->add('maxAcCurrent');
        $builder->add('frequency');
        $builder->add('powerFactor');
        $builder->add('euroEfficiency');
        $builder->add('acOutputConnection');
        $builder->add('nominalAcPower');
        $builder->add('phasesNumber');
        $builder->add('nominalAcCurrent');
        $builder->add('dcInputs');
        $builder->add('weight');
        $builder->add('nightConsumption');
        $builder->add('noiseLevel');
        $builder->add('transformer');
        $builder->add('protectionClass');
        $builder->add('interface');
        $builder->add('protectionFeatures');
        $builder->add($builder
                        ->create('powerRange', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );
        $builder->add($builder
                        ->create('mppVoltageRange', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );

        $builder->add($builder
                        ->create('frequencyRange', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );
        $builder->add($builder
                        ->create('operatingTemperature', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );
        $builder->add($builder
                        ->create('humidity', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );
        $builder->add($builder
                        ->create('outputAcVoltageRange', 'form')
                        ->add('min', 'number')
                        ->add('max', 'number')
        );

        $builder->add($builder
                        ->create('dimensions', 'form')
                        ->add('width', 'number')
                        ->add('height', 'number')
                        ->add('depth', 'number')
        );
        */

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
