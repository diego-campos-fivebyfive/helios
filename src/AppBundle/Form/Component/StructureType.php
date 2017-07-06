<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Component\StructureInterface;

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
                ;
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
