<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Service\Component\Query;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Component\MakerInterface;

class InverterType extends AbstractType
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

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
        $builder->add('minPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('maxPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('connection_type', null,[
            'required' => false
        ]);
        $builder->add('phases', null,[
            'required' => false
        ]);
        $builder->add('phase_voltage', null,[
            'required' => false
        ]);
        $builder->add('mppt_parallel', null,[
            'required' => false
        ]);
        $builder->add('mppt_connections', null,[
            'required' => false
        ]);
        $builder->add('code', TextType::class, [
            'label' => false
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
        $builder->add('alternative', ChoiceType::class, [
            'multiple' => false,
            'required' => false,
            'choices' => $this->getInverters($options)
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

    /**
     * @param $options
     * @return array
     */
    private function getInverters($options)
    {
        $updateInverter = $options['data'];

        $manager = $this->query->manager('inverter');

        $qb = $manager->createQueryBuilder();

        $qb->select('i.id, i.model, m.name')
            ->join(Maker::class, 'm', 'WITH', 'i.maker = m.id')
            ->orderBy('m.name');

        if($updateInverter->getId())
            $qb->where($qb->expr()->neq('i.id',$updateInverter->getId()));

        $inverters = $qb->getQuery()->getResult();

        $data = [];
        foreach ($inverters as $inverter) {
            if(!key_exists($inverter['name'], $data))
                $data[$inverter['name']] = [];
            $data[$inverter['name']][$inverter['id']] = $inverter['model'];
        }

        return $data;
    }
}
