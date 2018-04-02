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
        ini_set('memory_limit', '768M');

        /** @var Memorial $memorial */
        $memorial = $options['data'];

        $statuses = Memorial::getStatuses();

        $builder->add('name');

        if($memorial->getId() && !$memorial->getRanges()->isEmpty() && $memorial->isPending()){
            $builder->add('status', ChoiceType::class, [
                'choices' => $statuses
            ]);
        }
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
