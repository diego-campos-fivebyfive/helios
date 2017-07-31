<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Category;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Component\ProjectInterface $project */
        $project = $options['data'];
        $member = $project->getMember();
        $account = $member->getAccount();

        $customers = $member->getAllowedContacts()->toArray();

        uasort($customers, function(CustomerInterface $prev, CustomerInterface $next){
            return $prev->getFirstname() > $next->getFirstname();
        });

        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choices' => $customers,
                'required' => true
            ])
            ->add('stage', EntityType::class, [
                'class' => Category::class,
                'property' => 'sortedName',
                'query_builder' => function(EntityRepository $er) use($account){
                    return $er
                        ->createQueryBuilder('c')
                        ->where('c.account = :account')
                        ->andWhere('c.context = :context')
                        ->orderBy('c.position', 'asc')
                        ->setParameters([
                            'account' => $account,
                            'context' => Category::CONTEXT_SALE_STAGE
                        ]);
                }
            ])
            ->add('address')
            ->add('latitude')
            ->add('longitude')
            ->add('infConsumption', TextType::class, [
                'required' => true
            ])
            ->add('roofType', ChoiceType::class, [
                'choices' => Project::getRootTypes()
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }
}