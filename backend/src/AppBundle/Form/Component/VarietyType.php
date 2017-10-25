<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
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
            ->add('type', null, ['required' => false])
            ->add('required', null, ['required' => false])
            ->add('subtype', null, ['required' => false])
            ->add('code', null, ['required' => true])
            ->add('power', null, ['required' => false])
            ->add('description', null, ['required' => true])
            ->add('maker', null, ['required' => false])
            ->add('status', null, ['required' => false])
            ->add('promotional', null, ['required' => false])
            ->add('ncm', null, ['required' => false])
            ->add('cmvProtheus', null, ['required' => false])
            ->add('cmvApplied', null, ['required' => false]);

        $builder->add('available', CheckboxType::class, [
        'label' => 'Disponivel',
        'required' => false,
        //'disabled' => true
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


}
