<?php

namespace AppBundle\Form\Extra;

use AppBundle\Entity\Extra\AccountRegister;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

trait AccountHelperTypeTrait
{
    /**
     * @param FormBuilderInterface $builder
     */
    protected function addCompanyStatusField(FormBuilderInterface &$builder){

        $builder->add('companyStatus', ChoiceType::class, [
            'choices' => AccountRegister::getSupportedCompanyStatus(),
            'expanded' => true
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addCompanySectorField(FormBuilderInterface &$builder){

        $builder->add('companySector', ChoiceType::class, [
            'choices' => AccountRegister::getSupportedCompanySector()
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addCompanyMembersField(FormBuilderInterface &$builder){

        $builder->add('companyMembers', ChoiceType::class, [
            'choices' => AccountRegister::getSupportedCompanyMembers()
        ]);
    }
}