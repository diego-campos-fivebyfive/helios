<?php

namespace AdminBundle\Form;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        /** @var AccountInterface $account */
        $account  = $options['data'];
        $member = $options['member'];

        $isAdmin =  $member->isPlatformAdmin() || $member->isPlatformMaster();

        $accountId = $account->getId();
        $levels = Memorial::getDefaultLevels();
        $status = Customer::getStatusList();

        unset($status[Customer::PENDING], $status[Customer::ACTIVATED], $status[Customer::LOCKED]);
        unset($levels[Memorial::LEVEL_PROMOTIONAL]);

        $builder->add('document',TextType::class, array(
                'constraints' => new ContainsCnpj() ));
        $builder->add('extraDocument',TextType::class, [
                'required' => false ]);
        $builder->add('lastname',TextType::class);
        $builder->add('firstname',TextType::class);
        $builder->add('postcode',TextType::class);
        $builder->add('state',TextType::class);
        $builder->add('city',TextType::class);
        $builder->add('district',TextType::class);
        $builder->add('street',TextType::class);
        $builder->add('number',TextType::class, [
                'required' => false ]);
        $builder->add('level', ChoiceType::class, [
                'choices' => $levels
            ]);

        if ($isAdmin) {

            $platform = $member->getAccount();

            $members = $platform->getMembers()->filter(function (MemberInterface $member){
                return $member->isPlatformCommercial();
            });

            $builder->add('agent', EntityType::class,[
                'choices' => $members,
                'class' => 'AppBundle:Customer'
            ]);
        } else {
            $builder->remove('agent');
        }


        if (!$accountId) {

            $builder->add('members', CollectionType::class, [
                'entry_type' => OwnerType::class
            ]);

            $builder->add('status', ChoiceType::class, [
            'choices' => $status
            ]);

        } else {
            $builder->add('owner', OwnerType::class, [
                'data' => $account->getOwner(),
                'label' => false
            ]);
        }

        $builder->add('email',EmailType::class);
        $builder->add('phone',TextType::class);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
            'member' => null
        ));
    }

}
