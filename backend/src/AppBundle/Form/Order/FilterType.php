<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Order\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $statusNames = array_map('ucfirst', Order::getStatusNames());

        $optionsValues = [
            'power' => 'Potência',
            'total' => 'Valor'
        ];

        $builder
            ->add('status', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Status',
                'choices' => $statusNames,
                'multiple' => true
            ])
            ->add('like', TextType::class, [
                'required' => false
            ])
            ->add('statusAt', null, [
                'required' => false
            ])
            ->add('optionsVal', ChoiceType::class, [
                'choices' => $optionsValues
            ])
            ->add('valueMin', null, [
                'required' => false
            ])
            ->add('valueMax', null, [
                'required' => false
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
}
