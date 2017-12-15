<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Module;

class ModuleType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('maker', 'entity', array(
                'required' => true,
                'multiple' => false,
                'property' => 'name',
                'class' => 'AppBundle\Entity\Component\Maker',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er){

                    $parameters = ['context' => MakerInterface::CONTEXT_MODULE];

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
        $builder->add('cellType', ChoiceType::class, [
            'choices' => [
                'Monocrystalline' => 'Monocristalino',
                'Polycrystalline' => 'Policristalino',
                'Amorphous' => 'Amorfo'
            ]
        ]);
        $builder->add('cellNumber', TextType::class);

        $builder->add('maxPower', null, [
            'required' => true
        ]);

        $builder->add('voltageMaxPower', null, [
            'required' => true
        ]);
        $builder->add('currentMaxPower', null, [
            'required' => true
        ]);
        $builder->add('openCircuitVoltage', null, [
            'required' => true
        ]);
        $builder->add('shortCircuitCurrent', null, [
            'required' => true
        ]);
        $builder->add('efficiency', null, [
            'required' => true
        ]);
        $builder->add('temperatureOperation', TextType::class, [
            'required' => true
        ]);
        $builder->add('tempCoefficientMaxPower', null, [
            'required' => true
        ]);
        $builder->add('tempCoefficientOpenCircuitVoltage', MoneyType::class, [
            'currency' => '',
            'scale' => 3,
            'required' => true
        ]);
        $builder->add('tempCoefficientShortCircuitCurrent', MoneyType::class, [
            'currency' => '',
            'scale' => 3,
            'required' => true
        ]);
        $builder->add('code', TextType::class, [
            'label' => false
        ]);
        $builder->add('length', null, [
            'required' => false
        ]);
        $builder->add('width', null, [
            'required' => false
        ]);
        $builder->add('height', null, [
            'required' => false
        ]);
        $builder->add('connection_type', null, [
            'required' => false
        ]);
        $builder->add('position', NumberType::class, [
            'required' => false
        ]);
        $builder->add('princingLevels', ChoiceType::class, [
            'choices' => Memorial::getDefaultLevels(),
            'multiple' => true,
            'required' => false
        ]);
        $builder->add('generatorLevels', ChoiceType::class, [
            'choices' => Memorial::getDefaultLevels(),
            'multiple' => true,
            'required' => false
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Module',
            'is_validation' => false
        ));
    }

}
