<?php

namespace AdminBundle\Form\Order;

use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Order\FilterType as AppFilterType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AppFilterType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /** @var MemberInterface $member */
        $member = $options['member'];

        $builder->add('like', TextType::class, [
            'required' => false
        ]);

        if($member->isPlatformMaster() || $member->isPlatformAdmin()) {

            $account = $member->getAccount();

            $agents = $account->getMembers()->filter(function (MemberInterface $member){
                return $member->isPlatformCommercial();
            });

            $builder->add('agent', EntityType::class, [
                'required' => false,
                'placeholder' => 'Usuário',
                'class' => Customer::class,
                'choices' => $agents
            ]);
        }

        $builder->add('statusAt', null, [
            'required' => false
        ]);

        $builder->add('deliveryAt', null,  [
            'required' => false
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'member' => null
        ]);
    }
}
