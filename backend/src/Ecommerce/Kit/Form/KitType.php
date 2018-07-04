<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Kit\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', Type\TextType::class, [
                'required' => true
            ])
            ->add('description', Type\TextType::class, [
                'required' => true
            ])
            ->add('power', Type\MoneyType::class, [
                'currency' => false,
                'scale' => 2,
                'required' => true
            ])
            ->add('price', Type\MoneyType::class, [
                'currency' => false,
                'scale' => 2,
                'required' => true
            ])
            ->add('stock', Type\TextType::class, [
                'required' => true
            ])
            ->add('position', Type\TextType::class, [
                'required' => true
            ])
            ->add('available', Type\CheckboxType::class, [
                'required' => false
            ])
            ->add('components', null, [
                'mapped' => false,
                'required' => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
