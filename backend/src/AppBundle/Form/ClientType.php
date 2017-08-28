<?php

namespace AppBundle\Form;


use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\OAuthServerBundle\Model\Client;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class ClientType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('randomId', TextType::class)
            ->add('secret', TextType::class)
            ->add('allowedGrantTypes', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'token' => 'Token',
                    'authorization_code' => 'Authorization Code',
                    'client_credentials' => 'Client Credentials'
                ]
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ApiBundle\Entity\Client'
        ));
    }



}
