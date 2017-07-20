<?php
/**
 * Created by PhpStorm.
 * User: Fabio
 * Date: 22/06/2017
 * Time: 11:15
 */

namespace AppBundle\Form\Extra;

use AppBundle\Entity\Extra\AccountRegister;
use AppBundle\Util\Validator\Constraints\ContainsCnpj;
use AppBundle\Util\Validator\Constraints\ContainsCnpjValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PreRegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('document',TextType::class, array(
                'constraints' => new ContainsCnpj()
            ))
            ->add('extraDocument',TextType::class)
            ->add('lastname',TextType::class)
            ->add('firstname',TextType::class)
            ->add('postcode',TextType::class)
            ->add('state',TextType::class)
            ->add('city',TextType::class)
            ->add('district',TextType::class)
            ->add('street',TextType::class)
            ->add('number',TextType::class)

            ->add('contact',TextType::class)
            ->add('email',EmailType::class)
            ->add('phone',TextType::class);
        
    }

}