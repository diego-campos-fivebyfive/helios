<?php

namespace AdminBundle\Form;

use AppBundle\Entity\User;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('email', HiddenType::class, [
                'required' => true
            ])
            ->add('username', HiddenType::class, [
                'required' => true
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ]);

        if(!$user->isPlatformMaster()){

            $builder->add('roles', ChoiceType::class, [
                'choices' => User::getRolesOptions(),
                'expanded' => false,
                'multiple' => true
            ]);
        }

        if(!$options['data']->getPassword()) {
            $builder->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType');
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }

}
