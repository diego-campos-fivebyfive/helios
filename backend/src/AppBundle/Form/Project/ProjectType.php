<?php

namespace AppBundle\Form\Project;

use AppBundle\Configuration\Brazil;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Component\KitInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProjectInterface $project */
        $project = $options['data'];

        /** @var BusinessInterface $member */
        $member = $project->getMember();

        /** @var BusinessInterface $account */
        $account = $member->getAccount();

        $customers = $member->getAllowedContacts()->toArray();

        uasort($customers, function(BusinessInterface $prev, BusinessInterface $next){
            return $prev->getFirstname() > $next->getFirstname();
        });

        $kits = $account->getKits()->filter(function(KitInterface $kit){
            return $kit->isApplicable();
        })->toArray();

        uasort($kits, function(KitInterface $prev, KitInterface $next){
            return $prev->getPower() > $next->getPower();
        });

        $builder
            /*->add('state', 'choice', [
                'choices' => Brazil::states()
            ])
            ->add('city', ChoiceType::class, [
                'choices' => Brazil::cities($project->getState())
            ])*/
            ->add('latitude', 'text', [
                'attr' => [
                    //'readonly' => 'true'
                ]
            ])
            ->add('longitude', 'text', [
                'attr' => [
                    //'readonly' => 'true'
                ]
            ])
            ->add('address', TextType::class)
            ->add('kit', EntityType::class, [
                'class' => Kit::class,
                'choices' => $kits
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choices' => $customers,
                //'group_by' => 'context.id',
                'required' => false
                /*'choice_attr' => function(BusinessInterface $contact){
                    $icon = sprintf('fa-%s', $contact->isCompany() ? 'building' : 'user');
                    return ['data-icon' => $icon];
                }*/
            ])
            ->add('saleStage', EntityType::class, [
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
            ]);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\Project'
        ));
    }
}
