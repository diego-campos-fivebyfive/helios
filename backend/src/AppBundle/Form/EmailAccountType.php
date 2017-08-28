<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\EmailAccount;

class EmailAccountType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var EmailAccount $account */
        $account = $options['data'];

        $builder
            ->add('name')
            ->add('email')
            ->add('password', 'password')
            ->add('current');

        //if(!$account->getId()) {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => EmailAccount::getTypeList()
                ])
                ->add('inputServer')
                ->add('inputPort')
                ->add('inputEncryption', ChoiceType::class, [
                    'choices' => EmailAccount::getEncryptionList()
                ])
                ->add('outputServer')
                ->add('outputPort');
        //}
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\EmailAccount'
        ));
    }

}
