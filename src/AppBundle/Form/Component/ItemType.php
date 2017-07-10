<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => Item::getTypes(),
                'expanded' => true
            ])
            ->add('pricingBy', ChoiceType::class, [
                'choices' => Item::getPricingOptions(),
                'expanded' => true
            ])
            ->add('costPrice')        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Item'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_component_item';
    }


}
