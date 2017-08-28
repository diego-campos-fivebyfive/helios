<?php

namespace AppBundle\Form;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\TeamInterface;

class TeamType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $team = $options['data'];

        $builder->add('name');
        $builder->add('description', 'textarea', [
            'required' => false
        ]);
        $builder->add('enabled', ChoiceType::class, array(
            'choices' => array(
                TeamInterface::ENABLED => "Ativado",
                TeamInterface::DISABLED => "Desativado",
            ),
        ));
        
        $members = $team->getAccount()->getMembers()->filter(function(BusinessInterface $member) use($team){

            if(!$member->isOwner() && !$member->isAdmin()){

                //if(!$member->getTeam())
                    return true;
                //return $member->getTeam()->getId() == $team->getId();
            }

            return false;
        });

        $builder->add('members', 'entity', array(
                'required' => false,
                'multiple' => true,
                'class' => Customer::class,
                'choices' => $members,
                'group_by' => 'team.name'
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Team'
        ));
    }

}
