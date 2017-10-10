<?php

namespace AdminBundle\Form\Order;

use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Order\FilterType as AppFilterType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterType extends AppFilterType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('like', TextType::class, [
            'required' => false
        ]);
    }
}
