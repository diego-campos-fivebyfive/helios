<?php

namespace AdminBundle\Form\Component;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Variety;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DependencyType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $component = $options['component'];
        $dependencyIds = $this->filterDependencyIds($component->getDependencies());

        $builder
            ->add('type', HiddenType::class, [
                'data' => 'variety'
            ])
            ->add('id', EntityType::class, [
                'class' => Variety::class,
                'group_by' => 'type',
                'query_builder' => function(EntityRepository $er) use($dependencyIds){

                    $qb = $er->createQueryBuilder('c');

                    if(!empty($dependencyIds)) {
                        $qb->andWhere($qb->expr()->notIn('c.id', $dependencyIds));
                    }

                    return $qb;
                }
            ])
            ->add('ratio')
        ;

        $builder->get('id')->addModelTransformer(
            new CallbackTransformer(
                function($value){
                    return $value;
                },
                function(ComponentInterface $component){
                    return $component->getId();
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'component' => null
        ]);
    }

    /**
     * @param array $dependencies
     * @return array
     */
    private function filterDependencyIds(array $dependencies)
    {
        return array_map(function($dependency){
            return $dependency['id'];
        }, $dependencies);
    }
}
