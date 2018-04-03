<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Service\Component\Query;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StringBoxType extends AbstractType
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
            ->add('code', null, ['required' => true])
            ->add('description', null, ['required' => true])
            ->add('inputs', null, ['required' => true])
            ->add('outputs', null, ['required' => true])
            ->add('fuses', null, ['required' => true])
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

                        $parameters = ['context' => MakerInterface::CONTEXT_STRING_BOX];

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
            'choices' => $this->getStringBoxes($options)
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\StringBox'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_component_stringbox';
    }

    /**
     * @param $options
     * @return array
     */
    private function getStringBoxes($options)
    {
        $updateStringBox = $options['data'];

        $manager = $this->query->manager('String_box');

        $qb = $manager->createQueryBuilder();

        $qb->select('s.id, s.description, m.name')
            ->join(Maker::class, 'm', 'WITH', 's.maker = m.id')
            ->orderBy('m.name');

        if($updateStringBox->getId())
            $qb->where($qb->expr()->neq('s.id',$updateStringBox->getId()));

        $stringBoxes = $qb->getQuery()->getResult();

        $data = [];
        foreach ($stringBoxes as $stringBox) {
            if(!key_exists($stringBox['name'], $data))
                $data[$stringBox['name']] = [];
            $data[$stringBox['name']][$stringBox['id']] = $stringBox['description'];
        }

        return $data;
    }
}
