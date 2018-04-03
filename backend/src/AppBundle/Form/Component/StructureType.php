<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
use AppBundle\Service\Component\Query;
use AppBundle\Entity\Component\Maker;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\Structure;

class StructureType extends AbstractType
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
        $builder->add('maker', 'entity', array(
                'required' => true,
                'multiple' => false,
                'property' => 'name',
                'class' => 'AppBundle\Entity\Component\Maker',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er){

                    $parameters = ['context' => MakerInterface::CONTEXT_STRUCTURE];

                    $qb = $er
                        ->createQueryBuilder('m')
                        ->where('m.context = :context')
                        ->orderBy('m.name', 'ASC');

                    $qb->setParameters($parameters);

                    return $qb;
                }
            )
        );

        $builder->add('code', null, ['required' => false])
            ->add('description', null, ['required' => false])
            ->add('size', null, ['required' => false])
            ->add('position', NumberType::class, [
                'required' => false
            ])
            ->add('code', TextType::class);
        $builder->add('type', ChoiceType::class, [
            'required' => false,
            'multiple' => false,
            'choices' => Structure::getTypes()
        ]);
        $builder->add('subtype', ChoiceType::class, [
            'required' => false,
            'multiple' => false,
            'choices' => Structure::getSubtypes()
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
        $builder->add('minPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('maxPowerSelection', null, [
            'required' => false
        ]);
        $builder->add('alternative', ChoiceType::class, [
            'multiple' => false,
            'required' => false,
            'choices' => $this->getStructures($options)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Structure'
        ));
    }

    /**
     * @param $options
     * @return array
     */
    private function getStructures($options)
    {
        $updateStructure = $options['data'];

        $manager = $this->query->manager('structure');

        $qb = $manager->createQueryBuilder();

        $qb->select('s.id, s.description, m.name')
            ->join(Maker::class, 'm', 'WITH', 's.maker = m.id')
            ->orderBy('m.name');

        if($updateStructure->getId()) {
            $qb->where($qb->expr()->neq('s.id', $updateStructure->getId()));
        }

        $structures = $qb->getQuery()->getResult();

        $data = [];
        foreach ($structures as $structure) {
            if(!key_exists($structure['name'], $data)) {
                $data[$structure['name']] = [];
            }
            $data[$structure['name']][$structure['id']] = $structure['description'];
        }

        return $data;
    }
}
