<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    const TARGET_EDIT = 'edit';
    const TARGET_REVIEW = 'review';

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Order $order */
        $order = $options['data'];

        /** @var MemberInterface $member */
        $member = $options['member'];

        if(self::TARGET_EDIT == $options['target']) {
            $builder
                ->add('description');
        }

        if(self::TARGET_REVIEW == $options['target']){

            if($order->isBuilding() && !$order->getAccount()){

                $builder->add('account', EntityType::class, [
                    'class' => Customer::class,
                    'required' => false,
                    'query_builder' => function(EntityRepository $er) use($member){

                        $parameters = [
                            'context' => Customer::CONTEXT_ACCOUNT
                        ];

                        $qb = $er->createQueryBuilder('a');

                        $qb->where('a.context = :context');

                        if($member->isPlatformCommercial()){

                            $qb->andWhere('a.agent = :agent');

                            $parameters['agent'] = $member;
                        }

                        $qb->setParameters($parameters);

                        return $qb;
                    }
                ]);
            }

            $builder
                ->add('customer')
                ->add('cnpj')
                ->add('ie')
                ->add('contact')
                ->add('phone')
                ->add('email')
                ->add('postcode')
                ->add('address')
                ->add('city')
                ->add('state')
                ->add('paymentMethod', ChoiceType::class, [
                    'choices' => $options['paymentMethods']
                ])
                ->add('deliveryAt', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/YYYY'
                ])
                ->add('deadline')
            ;

        }
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Order::class,
            'target' => self::TARGET_EDIT,
            'paymentMethods' => []
        ])->setRequired([
            'member'
        ]);
    }

}
