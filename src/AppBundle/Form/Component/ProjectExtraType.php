<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Extra;
use AppBundle\Entity\Component\ProjectExtra;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectExtraType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Component\ProjectExtraInterface $projectExtra */
        $projectExtra = $options['data'];
        $project = $projectExtra->getProject();
        $account = $project->getMember()->getAccount();

        $type = $options['type'];

        $builder
            ->add('extra', EntityType::class, [
                'required' => true,
                'class' => Extra::class,
                'query_builder' => function(EntityRepository $er) use($account, $type){
                    return $er->createQueryBuilder('e')
                        ->where('e.account = :account')
                        ->andWhere('e.type = :type')
                        ->andWhere('e.deletedAt is null')
                        ->setParameters([
                            'account' => $account,
                            'type' => $type
                        ]);
                }
            ])
            ->add('quantity', TextType::class)
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectExtra::class,
            'type' => null
        ]);
    }
}