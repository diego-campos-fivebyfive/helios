<?php

namespace AppBundle\Form;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Form\MemberType;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('document',TextType::class, array(
                'constraints' => new ContainsCnpj()
            ))
            ->add('extraDocument',TextType::class, [
                'required' => false
            ])
            ->add('lastname',TextType::class)
            ->add('firstname',TextType::class)
            ->add('postcode',TextType::class)
            ->add('state',TextType::class)
            ->add('city',TextType::class)
            ->add('district',TextType::class)
            ->add('street',TextType::class)
            ->add('number',TextType::class, [
                'required' => false
            ])
            ->add('level', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels()
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Customer::getStatusList()
            ])
            /*->add('members', CollectionType::class, array(
                'entry_type' => MemberType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))*/
            ->add('email',EmailType::class)
            ->add('phone',TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Customer'
        ));
    }

}
