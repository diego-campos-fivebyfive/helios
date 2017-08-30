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
            ->add('code', null, ['required' => false])
            ->add('description', null, ['required' => false])
            ->add('inputs', null, ['required' => false])
            ->add('outputs', null, ['required' => false])
            ->add('fuses', null, ['required' => false])
            ->add('maker', null, ['required' => false])
            ->add('status', null, ['required' => false]);
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
