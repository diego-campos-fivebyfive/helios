<?php

namespace AppBundle\Form;

use AppBundle\Entity\BusinessInterface;
use Kolina\CustomerBundle\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailComposeType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var BusinessInterface $member */
        $member = $options['data']['member'];

        $contacts = $member->getAllowedContacts()->filter(function(BusinessInterface $contact){
            return $contact->getEmail();
        })->map(function(BusinessInterface $contact){
            return $contact->getEmail();
        })->toArray();

        $emails = array_combine(array_values($contacts), $contacts);

        $builder
            ->add('to', TextType::class)
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
            ->add('attachments', TextareaType::class, [
                'required' => false
            ])
        ;
    }

}