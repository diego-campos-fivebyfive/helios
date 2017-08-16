<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
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


        $builder->add('code')
                ->add('type')
                ->add('subtype')
                ->add('description')
                ->add('size')
                ->add('status', CheckboxType::class, [
                    'label'  => 'Ativo',
                    'required' => false
                ]);
        $builder->add('available', CheckboxType::class, [
            'label' => 'Disponivel',
            'disabled' => true
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
