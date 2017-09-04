<?php

namespace AppBundle\Form\Extra;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemorialFilterType extends AbstractType
{
    /**
     * @var \AppBundle\Manager\Pricing\MemorialManager
     */
    private $manager;

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->manager = $options['manager'];

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
                $this->addMemorialField($event->getForm());
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event){

            $form = $event->getForm();
            $data = $event->getData();

            if (array_key_exists('memorial', $data)) {
                $this->addLevelField($form, $data['memorial']);
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'manager' => null
        ]);
    }


    /**
     * @param FormBuilderInterface $builder
     */
    private function addMemorialField(FormInterface $form)
    {
        $form->add('memorial', EntityType::class, [
            'class' => Memorial::class
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function addLevelField(FormInterface $form, $memorial)
    {
        if(!$memorial instanceof Memorial){
            $memorial = $this->manager->find($memorial);
        }

        $used = [];
        $ranges = $memorial->getRanges()->filter(function(Range $range) use(&$used){

            $add = false;

            if(!in_array($range->getLevel(), $used)){
                $used[] = $range->getLevel();
                $add = true;
            }

            return $add;
        })->toArray();

        $levels = array_map(function(Range $range){
            return $range->getLevel();
        }, $ranges);

        $form->add('level', ChoiceType::class, [
            'choices' => array_combine($levels, $levels)
        ]);
    }
}