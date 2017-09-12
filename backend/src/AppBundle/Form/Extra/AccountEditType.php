<?php

namespace AppBundle\Form\Extra;

use AppBundle\Entity\AccountTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountEditType extends AbstractType
{

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

    /**
     * @param FormBuilderInterface $builder
     *
     */
    protected function addCompanyStatusField(FormBuilderInterface &$builder)
    {
        $builder->add('companyStatus', ChoiceType::class, [
            'choices' => self::getSupportedCompanyStatus(),
            'expanded' => true
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addCompanySectorField(FormBuilderInterface &$builder)
    {
        $builder->add('companySector', ChoiceType::class, [
            'choices' => self::getSupportedCompanySector()
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addCompanyMembersField(FormBuilderInterface &$builder)
    {
        $builder->add('companyMembers', ChoiceType::class, [
            'choices' => self::getSupportedCompanyMembers()
        ]);
    }

    /**
     * @return array
     */
    public static function getSupportedCompanyStatus()
    {
        return [
            'has_company' => 'I have company in the area of ​​photovoltaic solar energy',
            'open_company' => 'I want to open a company in the area of ​​photovoltaic solar energy'
        ];
    }

    /**
     * @return array
     */
    public static function getSupportedCompanySector()
    {
        return [
            'commerce' => 'Commercialization of equipment',
            'services' => 'Provision of services',
            'both' => 'Both'
        ];
    }

    /**
     * @return array
     */
    public static function getSupportedCompanyMembers()
    {
        return [
            '1-2' => '1 a 2',
            '3-4' => '3 a 4',
            '5-n' => 'Acima de 5'
        ];
    }

}