<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Parameter;
use AppBundle\Service\ProjectGenerator\Checker\Checker;
use AppBundle\Service\ProjectGenerator\ModuleProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneratorType extends AbstractType
{
    const INIT = 'init';
    const CHANGE = 'change';

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Checker $checker
     */
    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaults = $options['data'];

        $result = $this->checker->checkDefaults($defaults);

        $modules = $result['modules'];
        $inverterMakers = $result['inverter_makers'];
        $stringBoxMakers = $result['string_box_makers'];
        $structureMakers = $result['structure_makers'];
        $gridVoltages = $result['grid_voltages'];
        $gridPhaseNumbers = $result['grid_phase_numbers'];

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

        }else{

            $builder
                ->add('customer', ChoiceType::class, [
                    'choices' => []
                ])
                ->add('stage', ChoiceType::class, [
                    'choices' => []
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
                'choices' => $gridVoltages
            ])
            ->add('grid_phase_number', ChoiceType::class, [
                'choices' => $gridPhaseNumbers
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
                'choices' => $this->checker->checkRoofTypes()
            ])
            ->add('module', ChoiceType::class, [
                'choices' => self::createChoices($modules)
            ])
            ->add('inverter_maker', ChoiceType::class, [
                'choices' => self::createChoices($inverterMakers)
            ])
            ->add('structure_maker', ChoiceType::class, [
                'choices' => self::createChoices($structureMakers)
            ])
            ->add('string_box_maker', ChoiceType::class, [
                'choices' => self::createChoices($stringBoxMakers)
            ])
            ->add('inf_power', null, [
                'required' => false
            ])
            ->add('voltage', null, [
                'required' => false
            ])
            ->add('phases', null, [
                'required' => false
            ])
            ->add('power_increments', null, [
                'required' => false
            ])
        ;

        if(array_key_exists('account_level', $defaults)) {
            $this->addPromotionalSelector($builder, $defaults['account_level']);
            $this->addFinameSelector($builder);
        }
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

    /**
     * @param FormBuilderInterface $builder
     */
    private function addPromotionalSelector(FormBuilderInterface $builder, $level)
    {
        /** @var Parameter $parameter */
        $parameter = $this->checker->getEntityManager()->getRepository(Parameter::class)->find('platform_settings');
        $levels = (array) $parameter->get('promo_enabled_levels');

        if($parameter->get('enable_promo') && in_array($level, $levels)){

            $builder->add('is_promotional', CheckboxType::class, [
                'required' => false
            ]);

            $builder->add('promo_notice');
            $builder->add('promo_background');
        }
    }

    private function addFinameSelector(FormBuilderInterface $builder)
    {
        $parameter = $this->checker->getEntityManager()->getRepository(Parameter::class)->find('platform_settings');

        if ($parameter instanceof Parameter && $parameter->get('enable_finame')) {

            $builder->add('finame', CheckboxType::class, [
                'required' => false
            ]);

            $builder->add('finame_notice');
            $builder->add('finame_background');
        }
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    private function loadStages(AccountInterface $account)
    {
        $em = $this->checker->getEntityManager();

        $stages = $em->getRepository(Category::class)->findBy(
            ['account' => $account, 'context' => Category::CONTEXT_SALE_STAGE],
            ['position' => 'asc']
        );

        return $this->createChoices($stages);
    }

    /**
     * @param array $data
     * @return array
     */
    private function createChoices(array $data = [])
    {
        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, $data);

        $labels = array_map(function ($entity) {
            return (string) $entity;
        }, $data);

        $choices = array_combine($ids, $labels);

        return $choices;
    }
}
