<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Variety;
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


}
