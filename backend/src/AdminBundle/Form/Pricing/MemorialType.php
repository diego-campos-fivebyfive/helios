<?php

namespace AdminBundle\Form\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemorialType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Memorial $memorial */
        $memorial = $options['data'];

        $statuses = Memorial::getStatuses();

        if(!$memorial->getId()){
            unset($statuses[Memorial::STATUS_PUBLISHED], $statuses[Memorial::STATUS_EXPIRED]);
        }

        $builder
            ->add('name')
            ->add('status', ChoiceType::class, [
                'choices' => $statuses
            ])
            ->add('levels', ChoiceType::class, [
                'choices' => Memorial::getDefaultLevels(),
                'multiple' => true
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Memorial::class
        ]);
    }
}
