<?php


namespace AppBundle\Form\Signature;

use AppBundle\Model\Signature\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', TextType::class, [
                'required' => true
            ])
            ->add('number', TextType::class, [
                'required' => true
            ])
            ->add('additional_details', TextType::class, [
                'required' => false
            ])
            ->add('zipcode', TextType::class, [
                'required' => true
            ])
            ->add('neighborhood', TextType::class, [
                'required' => true
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('state', TextType::class, [
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ]);
            //->add('country');
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class
        ]);
    }
}