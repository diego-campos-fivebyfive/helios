<?php

namespace AppBundle\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Component\StructureInterface;

class StructureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $makerId = $options['is_validation'] ? $component->getMaker()->getId() : null;

        $builder->add(
            'maker', 'entity', array(
                'required' => true,
                'multiple' => false,
                'property' => 'name',
                'class' => 'AppBundle\Entity\Component\Maker',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($account, $makerId) {

                    $parameters = ['context' => MakerInterface::CONTEXT_INVERTER];

                    $qb = $er
                        ->createQueryBuilder('m')
                        ->where('m.context = :context')
                        ->orderBy('m.name', 'ASC');

                    if (!$makerId) {

                        $qb->andWhere(
                            $qb->expr()->orX(
                                'm.account is null',
                                'm.account = :account'
                            )
                        );

                        $parameters['account'] = $account;

                    } else {

                        $qb->andWhere('m.id = :id');

                        $parameters['id'] = $makerId;
                    }

                    $qb->setParameters($parameters);

                    return $qb;
                }
            )
        );

        $builder->add('code')
                ->add('type')
                ->add('subtype')
                ->add('description')
                ->add('size')
                ->add('token')
                ->add('model')
                ->add('status')
                ->add('maker')
                ->add('account')
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Structure'
        ));
    }
}
