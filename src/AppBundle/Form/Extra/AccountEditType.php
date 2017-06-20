<?php

namespace AppBundle\Form\Extra;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountEditType extends AbstractType
{
    use AccountHelperTypeTrait;

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formAttr = $builder->create('attributes', FormType::class);

        $formAttr->add('companyName', TextType::class);

        $this->addCompanyStatusField($formAttr);
        $this->addCompanySectorField($formAttr);
        $this->addCompanyMembersField($formAttr);

        $builder->add($formAttr);


    }

}