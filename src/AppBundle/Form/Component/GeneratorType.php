<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class GeneratorType extends AbstractType
{
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
        $builder
            ->add('power')
            ->add('module', ChoiceType::class, [
                'choices' => $this->loadModules()
            ])
            ->add('inverter_maker', ChoiceType::class, [
                'choices' => $this->loadInverterMakers()
            ])
            ->add('structure_maker', ChoiceType::class, [
                'choices' => $this->loadStructureMakers()
            ])
            ->add('roof_type', ChoiceType::class, [
                'choices' => Project::getRootTypes()
            ])
            ->add('string_box_maker', ChoiceType::class, [
                'choices' => $this->getStringBoxMakers()
            ])
        ;
    }

    /**
     * @return array
     */
    private function loadModules()
    {
        return $this->createChoices($this->em->getRepository(Module::class)->findBy([]));
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
        $ids = array_map(function($entity){
            return $entity->getId();
        }, $data);

        $labels = array_map(function($entity){
            return (string) $entity;
        }, $data);

        $choices = array_combine($ids, $labels);

        return $choices;
    }
}