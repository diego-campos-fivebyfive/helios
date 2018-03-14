<?php

namespace AdminBundle\Form;

use AppBundle\Configuration\Brazil;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

        unset($levels[Memorial::LEVEL_PROMOTIONAL]);
        unset($levels[Memorial::LEVEL_FINAME]);

        $builder->add('document',TextType::class, array(
                'constraints' => new ContainsCnpj() ));
        $builder->add('extraDocument',TextType::class, [
                'required' => false ]);
        $builder->add('lastname',TextType::class);
        $builder->add('firstname',TextType::class);
        $builder->add('postcode',TextType::class);
        $builder->add('state',ChoiceType::class, [
                'choices' => Brazil::states()
        ]);
        $builder->add('city',TextType::class);
        $builder->add('district',TextType::class);
        $builder->add('street',TextType::class);
        $builder->add('number',TextType::class, [
                'required' => false ]);
        $builder->add('level', ChoiceType::class, [
                'choices' => $levels
            ]);
        $builder->add('persistent', CheckboxType::class, [
                'required' => false
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

        } else {
            $builder->add('owner', OwnerType::class, [
                'data' => $account->getOwner(),
                'label' => false
            ]);
        }

        $builder->add('email',EmailType::class);
        $builder->add('phone',TextType::class);

        if (!$account->isParentAccount()) {
            $builder->add('parentAccount', EntityType::class, [
                'class' => Customer::class,
                'query_builder' => function (EntityRepository $er) use ($account) {
                    $qb = $er->createQueryBuilder('a');

                    $qb
                        ->where('a.context = :context')
                        ->andWhere('a.parent is null')
                        ->andWhere('a.status = :status')
                        ->setParameters([
                            'context' => BusinessInterface::CONTEXT_ACCOUNT,
                            'status' => 3
                        ])
                    ;

                    if ($account->getId()) {
                        $qb
                            ->andWhere('a.id <> :thisAccount')
                            ->setParameter(
                                'thisAccount', $account
                            )
                        ;
                    }

                    return $qb;
                },
                'choice_label' => 'firstname',
                'required' => false
            ]);
        }
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
