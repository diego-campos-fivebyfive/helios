<?php

namespace AppBundle\Form;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use Kolina\CustomerBundle\Form\CustomerType as AbstractUserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /** @var BusinessInterface $member */
        $member = $options['data'];
        /** @var BusinessInterface $currentMember */
        $currentMember = $options['current_member'];
        
        $isSameMember = $currentMember->getId() == $member->getId();
        $isActive = $member->getId() && $member->getUser();
        $isNameEditable = $isActive && $isSameMember;

        $isStatusEditable = (!$isSameMember && !$member->isMasterOwner());

        if(!$isActive){
            $builder->remove('firstname');
        }else{
            $builder->add('firstname', TextType::class, [
                'required' => true,
                'disabled' => !$isNameEditable
            ]);
        }

        $builder->add('email', EmailType::class, [
            'required' => true,
            'validation_groups' => ['member'],
            'disabled' => $isActive
        ]);

        $builder->add(
            $builder->create('attributes', FormType::class)
                ->add('is_owner', CheckboxType::class, [
                    'required' => false,
                    'disabled' => !$isStatusEditable
                ])
        );

        $this->removeExtraFields($builder);

        /*
        $builder
            ->add('office')
            ->add('isOwner', CheckboxType::class, [
                'required' => false
            ]);
            ->add(
                'team', EntityType::class, array(
                    'choice_label' => 'name',
                    'class' => Team::class,
                    'empty_data' => null,
                    'required' => false,
                    'placeholder' => 'Select a team',
                    'query_builder' =>
                        function (\Doctrine\ORM\EntityRepository $er) use ($options) {
                            $qb = $er->createQueryBuilder('p');
                            $qb->where('p.account = :account');
                            $qb->setParameter('account', $options['data']->getAccount());
                            return $qb->orderBy('p.name', 'ASC');
                        },
                )
            );*/
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'current_member' => null,
            'data_class' => Customer::class
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function removeExtraFields(FormBuilderInterface &$builder)
    {
        $fields = [
            'lastname', 'document', 'website', 'mobile', 'phone', 'fax',
            'postcode', 'country', 'state', 'city', 'district', 'street', 'number', 'complement',
            'user'
        ];

        foreach($fields as $field){
            if($builder->has($field)){
                $builder->remove($field);
            }
        }
    }
}
