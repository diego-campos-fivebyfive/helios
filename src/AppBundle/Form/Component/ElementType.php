<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\KitElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var KitElement $element */
        $element = $options['data'];

        $kit = $element->getKit();

        $strategies = KitElement::getPriceStrategies();

        /*
         * Removed
         *
        if($element->isElement()) {
            unset($strategies[KitElement::PRICE_STRATEGY_PERCENTAGE]);
        }else{
            $builder
                ->add('rate', 'text')
            ;
        }*/

        //dump($kit->getInvoicePriceStrategy()); die;

        $builder
            ->add('name')
        ;

        if ($element->isService() || KitElement::PRICE_STRATEGY_INCREMENTAL == $kit->getInvoicePriceStrategy()) {
            $builder
                ->add('priceStrategy', 'choice', [
                    'choices' => $strategies
                ])
                ->add('unitPrice', MoneyType::class,[
                    'currency' => false,
                    'required' => false
                ]);

        }

        if($element->isElement()){
            $builder->add('quantity', TextType::class);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\KitElement'
        ));
    }
}