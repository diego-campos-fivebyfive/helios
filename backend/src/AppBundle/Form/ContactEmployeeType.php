<?php

namespace AppBundle\Form;

use AppBundle\Entity\BusinessInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ContactEmployeeType extends CustomerType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('office');

        $removes = [
                'user', 'lastname', 'document', 'website', 'postcode',
                'country', 'state', 'city', 'district', 'street', 'number', 'complement'
        ];

        foreach($removes as $field){
            $builder->remove($field);
        }

        $builder->add('context', 'entity', [
            'class' => 'AppBundle:Context',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('c')
                          ->where('c.id = :id')
                          ->setParameter('id', BusinessInterface::CONTEXT_PERSON)
                    ;
            }
        ]);
    }

}