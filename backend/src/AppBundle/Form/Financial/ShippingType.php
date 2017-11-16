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
        $enablePromo = $parameters->get('enable_promo');
        $shippingIncluded = $parameters->get('shipping_included');
        $shipping_included_max_power = $parameters->get('shipping_included_max_power');

        $max_power_valid = $shipping_included_max_power < $order->getPower();

        $choices = [];

        $isFullyPromotional = false;
        if (!$isProject)
            $isFullyPromotional = $order->isFullyPromotional();

        if (!$isProject && $isFullyPromotional && $enablePromo && $shippingIncluded && $max_power_valid)
            $choices = [
                'included' => 'Frete Incluso',
                'self' => 'Meu Frete'
            ];

        elseif (!$isProject && $member->isPlatformUser() && $shippingIncluded)
            $choices = [
                'included' => 'Frete Incluso',
                'sices' => 'Frete Sices',
                'self' => 'Meu Frete'
            ];

         else
             $choices = [
                 'sices' => 'Frete Sices',
                 'self' => 'Meu Frete'
             ];

        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $choices
            ])
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
            ]);

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
