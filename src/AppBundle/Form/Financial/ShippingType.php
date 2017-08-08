<?php

namespace AppBundle\Form\Financial;

use AppBundle\Configuration\Brazil;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'self' => 'Meu Frete',
                    'sices' => 'Frete Sices'
                ]
            ])
            ->add('state', ChoiceType::class, [
                'choices' => Brazil::states()
            ])
            ->add('kind', ChoiceType::class, [
                'choices' => [
                    'interior' => 'Interior',
                    'capital' => 'Capital'
                ]
            ])
        ;
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