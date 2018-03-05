<?php

namespace AdminBundle\Form;

use AdminBundle\Form\Settings\Orders\ExpirationType;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Pricing\MemorialInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('parameters', FormType::class)
                ->add(
                    $builder
                        ->create('platform', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add(
                    $builder
                        ->create('master', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add(
                    $builder
                        ->create('admin', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )->add(
                    $builder
                        ->create('financial', FormType::class)
                        ->add('name', TextType::class)
                        ->add('email', EmailType::class)
                )
                ->add('enable_promo', CheckboxType::class)
                ->add('promo_notice', TextType::class, [
                    'required' => false
                ])
                ->add('promo_background', null, [
                    'required' => false
                ])
                ->add('enable_finame', CheckboxType::class, [
                    'required' => false
                ])
                ->add('finame_notice', TextType::class, [
                    'required' => false
                ])
                ->add('finame_background', null, [
                    'required' => false
                ])
                ->add('finame_shipping_included', CheckboxType::class)
                ->add('finame_shipping_included_max_power', TextType::class)
                ->add('shipping_included', CheckboxType::class)
                ->add('shipping_included_max_power', TextType::class)
                ->add('enabled_roof_types', ChoiceType::class, [
                        'multiple' => true,
                        'expanded' => true,
                        'choices'  => Project::getRoofTypes(),
                        'choice_attr' => function() {
                            return ['class' => 'choices-roof'];
                        }
                    ]
                )
                ->add('fdi_min', TextType::class)
                ->add('fdi_max', TextType::class)
                ->add('promo_end_at', TextType::class)
                ->add('max_order_discount', TextType::class)
                ->add('max_commercial_discount', TextType::class)
                ->add(
                    $builder->create('order_expiration_days', CollectionType::class, [
                        'entry_type' => ExpirationType::class,
                        'allow_add' => true,
                        'entry_options' => [
                            'label' => false
                        ],
                        'allow_delete' => true
                    ])
                )
                ->add(
                    $builder
                        ->create('account_level_handler', FormType::class)
                        ->add($builder
                            ->create('levels', FormType::class)
                            ->add(
                                $builder
                                    ->create(MemorialInterface::LEVEL_PARTNER, FormType::class)
                                    ->add('amount', TextType::class)
                                    ->add('days', TextType::class)
                            )
                            ->add(
                                $builder
                                    ->create(MemorialInterface::LEVEL_PREMIUM, FormType::class)
                                    ->add('amount', TextType::class)
                                    ->add('days', TextType::class)
                            )
                            ->add(
                                $builder
                                    ->create(MemorialInterface::LEVEL_PLATINUM, FormType::class)
                                    ->add('amount', TextType::class)
                                    ->add('days', TextType::class)
                            )
                            ->add(
                                $builder
                                    ->create(MemorialInterface::LEVEL_BLACK, FormType::class)
                                    ->add('amount', TextType::class)
                                    ->add('days', TextType::class)
                            )
                        )
                        ->add('grace_period', TextType::class)
                )
                ->add('coupon_order_percent', TextType::class)
                ->add('coupon_step_options', TextType::class)
        );
        $builder->get('parameters')
            ->get('promo_end_at')
            ->addModelTransformer(new CallbackTransformer(
                function ($endAt) {
                    return (new \DateTime($endAt['date']))->format('d/m/Y');
                },
                function ($endAt) {
                    return new \DateTime(implode('-', array_reverse(explode('/', $endAt))));
                }
            ));
    }
}
