<?php

namespace AppBundle\Form\Extra;

use AppBundle\Entity\Extra\AccountRegister;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountRegisterType extends AbstractType
{
    use AccountHelperTypeTrait;

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AccountRegister $register */
        $register = $options['data'];

        if($register->isInit()) {
            $builder->add('email', EmailType::class, [
                'required' => true
            ]);
        }

        if ($register->isInfo()) {

            $builder
                ->add('name', TextType::class, [
                    'required' => true,
                    'validation_groups' => [AccountRegister::STAGE_INFO]
                ])
                ->add('phone', TextType::class, [
                    'required' => true
                ])
                ->add('companyName', TextType::class, [
                    'required' => false
                ]);

            $this->addCompanyStatusField($builder);

            $this->addCompanySectorField($builder);

            $this->addCompanyMembersField($builder);
        }
    }

}