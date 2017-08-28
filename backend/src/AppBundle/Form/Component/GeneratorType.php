<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\MemberInterface;
use AppBundle\Service\ProjectGenerator\ModuleProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneratorType extends AbstractType
{
    const INIT = 'init';
    const CHANGE = 'change';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @inheritDoc
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $member = $options['member'];

        if($member instanceof MemberInterface) {

            $customers = $member->getAllowedContacts()->toArray();

            $builder
                ->add('customer', ChoiceType::class, [
                    'choices' => $this->createChoices($customers)
                ])
                ->add('stage', ChoiceType::class, [
                    'choices' => $this->loadStages($member->getAccount())
                ]);
        }

        $builder
            ->add('account_id', TextType::class, [
                'required' => false
            ])
            ->add('address', TextType::class, [
                'required' => false
            ])
            ->add('grid_voltage', ChoiceType::class, [
                'choices' => [
                    '127/220' => '127/220',
                    '220/380' => '220/380'
                ]
            ])
            ->add('grid_phase_number', ChoiceType::class, [
                'choices' => [
                    'Monophasic' => 'Monophasic',
                    'Biphasic' => 'Biphasic',
                    'Triphasic' => 'Triphasic'
                ]
            ])
            ->add('use_transformer', CheckboxType::class, [
                'required' => false
            ])
            ->add('power', MoneyType::class, [
                'currency' => false,
            ])
            ->add('consumption')
            ->add('latitude', TextType::class, [
                'required' => false
            ])
            ->add('longitude', TextType::class, [
                'required' => false
            ])
            ->add('source', ChoiceType::class, [
                'choices' => [
                    'power' => 'Power',
                    'consumption' => 'Consumption'
                ]
            ])
            ->add('roof_type', ChoiceType::class, [
                'choices' => Project::getRoofTypes()
            ])
            ->add('module', ChoiceType::class, [
                'choices' => $this->loadModules()
            ])
            ->add('inverter_maker', ChoiceType::class, [
                'choices' => $this->loadInverterMakers()
            ])
            ->add('structure_maker', ChoiceType::class, [
                'choices' => $this->loadStructureMakers()
            ])
            ->add('string_box_maker', ChoiceType::class, [
                'choices' => $this->getStringBoxMakers()
            ])
            ->add('is_promotional', CheckboxType::class, [
                'required' => false
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'status' => self::CHANGE,
            'member' => null,
            'csrf_protection' => false
        ]);
    }

    private function loadStages(AccountInterface $account)
    {
        $stages = $this->em->getRepository(Category::class)->findBy(
            ['account' => $account, 'context' => Category::CONTEXT_SALE_STAGE],
            ['position' => 'asc']
        );

        return $this->createChoices($stages);
    }

    /**
     * @return array
     */
    private function loadModules()
    {
        return $this->createChoices($this->em->getRepository(Module::class)->findBy(ModuleProvider::$criteria));
    }

    /**
     * @return array
     */
    private function loadInverterMakers()
    {
        return $this->createChoices($this->loadMakers(Maker::CONTEXT_INVERTER));
    }

    /**
     * @return array
     */
    private function loadStructureMakers()
    {
        return $this->createChoices($this->loadMakers(Maker::CONTEXT_STRUCTURE));
    }

    /**
     * @return array
     */
    private function getStringBoxMakers()
    {
        return $this->createChoices($this->loadMakers(Maker::CONTEXT_STRING_BOX));
    }

    /**
     * @param $context
     * @return array
     */
    private function loadMakers($context)
    {
        return $this->em->getRepository(Maker::class)->findBy([
            'context' => $context
        ]);
    }

    /**
     * @param array $data
     */
    private function createChoices(array $data = [])
    {
        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, $data);

        $labels = array_map(function ($entity) {
            return (string)$entity;
        }, $data);

        $choices = array_combine($ids, $labels);

        return $choices;
    }
}