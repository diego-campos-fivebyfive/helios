<?php

namespace AdminBundle\Form\Account;

use AppBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\MemberInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $account = $options['account'];

        $members = $account->getMembers()->filter(function (MemberInterface $member) {
            return $member->isPlatformCommercial();
        });

        $builder
            ->add('source', EntityType::class,[
                'choices' => $members,
                'class' => 'AppBundle:Customer'
            ])
            ->add('target', EntityType::class,[
                'choices' => $members,
                'class' => 'AppBundle:Customer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'account' => null
        ));
    }
}
