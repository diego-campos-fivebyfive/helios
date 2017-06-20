<?php

namespace AppBundle\Form;

use AppBundle\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExplorerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Customer $member */
        $member = $options['data']['member'];

        $contacts = $member->getAllowedContacts();

        $builder
            ->add('contact', EntityType::class,[
                'class' => Customer::class,
                'choices' => $contacts,
                'group_by' => 'context'
            ]);
    }
}