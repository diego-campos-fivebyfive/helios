<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Customer;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$member = $options['data'];

        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => false
            ]);

        /*if (!$member) {
            $builder
                ->add('email', HiddenType::class)
                ->add('phone', HiddenType::class);
        } else {
            $builder
                ->remove('email', EmailType::class)
                ->remove('phone', NumberType::class);
        }*/

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class
        ));
    }
}
