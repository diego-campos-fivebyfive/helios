<?php
namespace AdminBundle\Form\Misc;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Misc\Additive;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InsuranceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, [
                'required' => false
            ])
            ->add( 'name', TextType::class, [
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('target', ChoiceType::class, [
                'required' => true,
                'choices' => Additive::getTargets()
            ])
            ->add('value', TextType::class, [
                'required' => true
            ])
            ->add('requiredLevels', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels(),
                'required' => false,
                'multiple' => true
            ])
            ->add('availableLevels', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels(),
                'required' => false,
                'multiple' => true
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Misc\Additive'
        ));
    }

}
