<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Service\CustomerHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Kolina\CustomerBundle\Entity\CustomerInterface;
use Kolina\CustomerBundle\Form\CustomerType as AbstractCustomerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\BusinessInterface;

class ContactType extends AbstractCustomerType
{
    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @inheritDoc
     */
    public function __construct(CustomerHelper $customerHelper)
    {
        $this->customerHelper = $customerHelper;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $contact = $options['data'];

        /** @var BusinessInterface $member */
        $member = $contact->getMember();
        $members = $member->getAccount()->getMembers();
        $categories = $member->getAccount()->getCategories(Category::CONTEXT_CONTACT);

        $builder->add('category', 'entity', [
            'class' => Category::class,
            'choices' => $categories
        ]);

        $builder->add('information');

        $builder->add($builder->create('coordinates', 'form', [
                    'required' => false
                ])
                ->add('latitude', 'text')
                ->add('longitude', 'text')
        );

        $builder->remove('user')
                ->remove('lastname')
                ->remove('document')
        ;

        $builder->get('firstname')->setRequired(true);

        if($contact->isPerson()){

            $companies = $contact->getMember()->getAllowedCompanies();

            $builder->add('company', 'entity', [
                'required' => false,
                'class' => 'AppBundle:Customer',
                'choices' => $companies,
                'placeholder' => 'Select a company',
                'group_by' => 'member'
            ]);

            $builder->add('title');
        }

        if($contact->isCompany()){

            // 1. This method create new persons associations
            $builder->add('employees', CollectionType::class, [
                'entry_type' => ContactEmployeeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ]);

            // 2. This method related existing persons - accessible
            $persons = $contact->getMember()->getAllowedPersons()->filter(function(BusinessInterface $person){
                return !$person->getCompany();
            });

            $choices = [];
            foreach($persons as $person){
                if($person instanceof BusinessInterface)
                $choices[$person->getId()] = $person->getName();
            }

            $builder->add('relatedEmployees', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => true,
                'required' => false
            ]);
        }

        /**
         * Only MEMBER a current ACCOUNT
         */
        $builder->add('accessors', 'entity', [
            'class' => 'AppBundle:Customer',
            'choices' => $members->filter(function(BusinessInterface $member) use($contact){
                return $member->getId() != $contact->getMember()->getId() && !$member->isOwner();
            }),
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ]);

        $builder->add('document');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Customer'
        ));
    }
}