<?php

namespace AppBundle\Form\Financial;

use AppBundle\Configuration\Brazil;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $member = $options['member'];
        $isProject = $options['isProject'];

        $rule = $options['rule'];
        if (!$rule)
            $rule['type'] = 'self';

        $order = $options['order'];
        $parameters = $options['parameters'];
        $enablePromo = $parameters->getParameters()['enable_promo'];
        $shippingIncluded = $parameters->getParameters()['shipping_included'];

        $choices = [];

        if (!$isProject && !$member->isPlatformUser() && $rule['type'] == 'included'
            || ($enablePromo == true && $shippingIncluded == true && $order->isFullyPromotional())) {

            $choices['included'] = 'Frete Incluso';

        } elseif (!$isProject && $member->isPlatformUser() && $shippingIncluded) {

            $choices = [
                'self' => 'Meu Frete',
                'sices' => 'Frete Sices',
                'included' => 'Frete Incluso'
            ];
        } else {
            $choices = [
                'self' => 'Meu Frete',
                'sices' => 'Frete Sices'
            ];
        }

        $builder

            ->add('type', ChoiceType::class, [
                'choices' => $choices
            ]);

        $builder
            ->add('state', ChoiceType::class, [
                'choices' => Brazil::states()
            ])
            ->add('percent', MoneyType::class,[
                'currency' => false
            ])
            ->add('kind', ChoiceType::class, [
                'choices' => [
                    'interior' => 'Interior',
                    'capital' => 'Capital'
                ]
            ])
        ;

        $builder->get('percent')->addModelTransformer(new CallbackTransformer(
            function($percent){
                return $percent * 100;
            },
            function($percent){
                return $percent / 100;
            }
        ));

        $builder->get('kind')->addModelTransformer(new CallbackTransformer(
            function($kind){
                $blocks = array_reverse(explode('-', $kind));
                return $blocks[0];
            },
            function($kind){
                return $kind;
            }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'member' => null,
            'order' => null,
            'rule' => null,
            'parameters' => null,
            'isProject' => null
        ));
    }

    /**
     * @param array $data
     */
    public static function normalize(array &$data)
    {
        foreach(Brazil::regions() as $region => $states){
            if(in_array($data['state'], $states)){
                $data['region'] = $region;
                break;
            }
        }

        if('capital' == $data['kind'] && in_array($data['state'], ['RJ', 'SP', 'MG'])){
            $data['kind'] = strtolower(sprintf('%s-capital', $data['state']));
        }
    }
}
