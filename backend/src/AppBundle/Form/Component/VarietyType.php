<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Variety;
use AppBundle\Service\Component\Query;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VarietyType extends AbstractType
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => Variety::getTypes(),
                'placeholder' => false,
                'multiple' => false,
                'required' => true
            ])
            ->add('required', null, ['required' => false])
            ->add('subtype', null, ['required' => false])
            ->add('code', null, ['required' => true])
            ->add('power', null, ['required' => false])
            ->add('description', null, ['required' => true])
            ->add('position', NumberType::class, [
                'required' => false
            ])
            ->add('princingLevels', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels(),
                'multiple' => true,
                'required' => false
            ])
            ->add('generatorLevels', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels(),
                'multiple' => true,
                'required' => false
            ])
            ->add('maker', 'entity', array(
                    'required' => true,
                    'multiple' => false,
                    'property' => 'name',
                    'class' => 'AppBundle\Entity\Component\Maker',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er){

                        $parameters = ['context' => MakerInterface::CONTEXT_VARIETY];

                        $qb = $er
                            ->createQueryBuilder('m')
                            ->where('m.context = :context')
                            ->orderBy('m.name', 'ASC');

                        $qb->setParameters($parameters);

                        return $qb;
                    }
                )
            );

        $builder->add('minPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('maxPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('alternative', ChoiceType::class, [
            'multiple' => false,
            'required' => false,
            'choices' => $this->getVarieties($options)
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Variety'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_component_variety';
    }

    /**
     * @param $options
     * @return array
     */
    private function getVarieties($options)
    {
        $updateVariety = $options['data'];

        $manager = $this->query->manager('variety');

        $qb = $manager->createQueryBuilder();

        $qb->select('v.id, v.description, m.name')
            ->join(Maker::class, 'm', 'WITH', 'v.maker = m.id')
            ->orderBy('m.name');

        if($updateVariety->getId()) {
            $qb->where($qb->expr()->neq('v.id', $updateVariety->getId()));
        }

        $varieries = $qb->getQuery()->getResult();

        $data = [];
        foreach ($varieries as $variety) {
            if(!key_exists($variety['name'], $data)) {
                $data[$variety['name']] = [];
            }
            $data[$variety['name']][$variety['id']] = $variety['description'];
        }

        return $data;
    }
}
