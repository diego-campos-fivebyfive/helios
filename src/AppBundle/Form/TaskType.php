<?php

namespace AppBundle\Form;

use AppBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\TaskInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\BusinessInterface;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', 'datetime', array(
                    'required' => true,
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'date_format' => 'dd/MM/yyyy'
                )
            )
            ->add('endAt', 'datetime', array(
                    'required' => true,
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'date_format' => 'dd/MM/yyyy'
                )
            )
            ->add('description', TextareaType::class, [
                'required' => true
            ])
            ->add('type', ChoiceType::class, array(
                'choices' => Task::getTypes(),
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ));

        /** @var Task $task */
        $task = $options['data'];
        $member = $task->getAuthor();

        /** @var \AppBundle\Entity\BusinessInterface $manipulator */
        $manipulator = $options['manipulator'];

        //$task->setType(Task::TYPE_UNDEFINED);

        if (!$member instanceof BusinessInterface || !$member->isMember())
            throw new \Exception('Invalid member');

        $contacts = $member->isOwner() ? $member->getAccountContacts() : $member->getAllowedContacts();
        $requiredContact = false;
        $currentContact = $task->getContact();

        if($task->getId() && $currentContact && $currentContact->isDeleted()){
            $contacts = [$currentContact];
            $requiredContact = true;
        }

        $builder->add('contact', 'entity', array(
                'multiple' => false,
                'required' => $requiredContact,
                'property' => 'firstname',
                'class' => Customer::class,
                'choices' => $contacts,
                'group_by' => 'context',
                'choice_translation_domain' => true
            )
        );

        $builder->add('members', 'entity', array(
                'multiple' => true,
                'required' => false,
                'property' => 'firstname',
                'class' => Customer::class,
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($member, $manipulator) {

                    $qb = $er->createQueryBuilder('m');
                    $qb->where('m.account = :account')
                        ->andWhere('m.deletedAt is null')
                        ->orderBy('m.firstname', 'ASC')
                        ->andWhere('m.id <> :id')
                        ->setParameters([
                            'account' => $member->getAccount(),
                            'id' => $manipulator->getId()
                        ]);

                    return $qb;
                }
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task',
            'manipulator' => null
        ));
    }
}
