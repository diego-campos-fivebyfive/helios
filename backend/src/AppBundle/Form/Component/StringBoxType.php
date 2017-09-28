<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StringBoxType extends AbstractType
{
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
            ->add('maker', null, ['required' => true])
            ->add('status', null, ['required' => false])
            ->add('ncm', null, ['required' => true])
            ->add('cmvProtheus', null, ['required' => true])
            ->add('cmvApplied', null, ['required' => true])
            ->add('promotional', null, ['required' => false]);
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


}
