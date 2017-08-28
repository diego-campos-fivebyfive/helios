<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Catalog;
use AppBundle\Entity\Component\Kit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['final']) {
            
            $builder
                ->add('identifier', TextType::class, [
                    'attr' => [
                        'maxlength' => 30
                    ]
                ])
                ->add('invoicePriceStrategy', ChoiceType::class, [
                    'choices' => Kit::getInvoicePriceStrategies(),
                    'expanded' => true
                ]);

        } else {

            /** @var Kit $kit */
            $kit = $options['data'];

            if (Kit::PRICE_STRATEGY_ABS == $kit->getInvoicePriceStrategy()) {

                $builder->add('invoiceBasePrice', MoneyType::class, [
                    'currency' => false,
                    'scale' => 2
                ]);
            }

            $builder
                ->add('deliveryPriceStrategy', ChoiceType::class, [
                    'choices' => Kit::getDeliveryPriceStrategies(),
                ])
                ->add('deliveryBasePrice', MoneyType::class, [
                    'currency' => false,
                    'scale' => 2
                ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Kit',
            'final' => false
        ));
    }
}