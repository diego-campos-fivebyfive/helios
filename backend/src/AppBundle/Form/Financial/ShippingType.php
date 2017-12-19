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
        $shippingIncludedMaxPower = $parameters->get('shipping_included_max_power');

        $enableFiname = $parameters->get('enable_finame');
        $finameShippingIncluded = $parameters->get('finame_shipping_included');
        $finameShippingIncludedMaxPower = $parameters->get('finame_shipping_included_max_power');

        $maxPowerValid = $shippingIncludedMaxPower ? $shippingIncludedMaxPower >= $order->getPower() : $finameShippingIncludedMaxPower >= $order->getPower();

        $isFullyPromotional = $isProject ? false : $order->isFullyPromotional();
        $allowShippingIncluded = !$isProject && $isFullyPromotional && $enablePromo && $shippingIncluded && $maxPowerValid;

        $isFullyFiname = $isProject ? false : $order->isFullyFiname();
        $allowFinameShippingIncluded = !$isProject && $isFullyFiname && $enableFiname && $finameShippingIncluded && $maxPowerValid;

        $choices = [
            'included' => 'Frete Incluso',
            'sices' => 'Frete Sices',
            'self' => 'Meu Frete'
        ];

        if(!$member->isPlatformUser()){
            if(!$allowShippingIncluded && !$allowFinameShippingIncluded)
                unset($choices['included']);
            else
                unset($choices['sices']);
        }

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
