<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('code', null, ['required' => false])
            ->add('type', null, ['required' => false])
            ->add('subtype', null, ['required' => false])
            ->add('description', null, ['required' => false])
            ->add('size', null, ['required' => false])
            ->add('ncm', null, ['required' => true])
            ->add('cmvProtheus', null, ['required' => true])
            ->add('cmvApplied', null, ['required' => true])
            ->add('promotional', null, ['required' => false])
            ->add('status', CheckboxType::class, [
                'label'  => 'Ativo',
                'required' => false
            ]);
        $builder->add('available', CheckboxType::class, [
            'label' => 'Disponível',
            'required' => false
        ])
            ->add('position', NumberType::class, [
                'required' => false
            ])
            ->add('code', TextType::class);
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Structure'
        ));
    }
}
