<?php

namespace AppBundle\Form\Component;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\KitComponentInterface;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitInverterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $form = $builder->getForm();

        /** @var KitComponentInterface $kitComponent */
        $kitComponent = $form->getData();
        $kitComponent->makeHelpers();

        $kit = $kitComponent->getKit();
        $account = $kit->getAccount();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($kitComponent, $account) {

            $form = $event->getForm();

            $this->addMakerField($form, $account);

            if (null != $component = $kitComponent->getInverter()) {
                /*$this->addSerialField($form, $component->getMaker()->getId());
                $this->addComponentField($form, $component->getSerial());*/
                $this->addComponentField($form, $component->getMaker()->getId(), $account);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($account) {

            $form = $event->getForm();
            $data = $event->getData();

            if (array_key_exists('maker', $data)) {
                //$this->addSerialField($form, (int) $data['maker']);
                $this->addComponentField($form, (int)$data['maker'], $account);
            }

            /*if(array_key_exists('serial', $data)){
                $this->addComponentField($form, $data['serial']);
            }*/
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Component\KitComponent'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function addMakerField(FormInterface &$form, BusinessInterface $account)
    {
        $form->add('maker', EntityType::class, [
            'placeholder' => 'select.maker',
            'class' => Maker::class,
            'query_builder' => function (EntityRepository $er) use($account) {

                $qb = $er->createQueryBuilder('m');

                /*
                $qb->where($qb->expr()->in(
                    'm.context', [Maker::CONTEXT_INVERTER, Maker::CONTEXT_ALL]
                ))->join('m.inverters', 'i');
                */

                $qb
                    ->where('m.context = :context')
                    ->andWhere(
                        $qb->expr()->orX(
                            'm.account is null',
                            'm.account = :account'
                        )
                    )
                    ->orderBy('m.name', 'ASC')
                    ->setParameters([
                        ':context' => MakerInterface::CONTEXT_INVERTER,
                        ':account' => $account
                    ])
                ;

                return $qb;
            }
        ]);
    }

    /**
     * @deprecated      This option is removed
     * @param FormInterface $form
     */
    private function addSerialField(FormInterface &$form, $makerId)
    {
        /*$form->add('serial', EntityType::class, [
            'placeholder' => 'select.serial',
            'class' => Inverter::class,
            'choice_label' => 'serial',
            'choice_value' => 'serial',
            'query_builder' => function(EntityRepository $er) use($makerId){

                return $er->createQueryBuilder('c')
                    ->where('c.maker = :maker')
                    ->groupBy('c.serial')
                    ->setParameter('maker', $makerId);
            }
        ]);*/
    }

    private function addComponentField(FormInterface &$form, $maker, $account)
    {
        $form
            ->add('inverter', EntityType::class, [
                'placeholder' => 'select.model',
                'class' => Inverter::class,
                'choice_label' => function(ComponentInterface $component){
                    $model = $component->getModel();
                    return $component->isPrivate() ? $model . ' [Meu Componente]' : $model ;
                },
                'query_builder' => function (EntityRepository $er) use ($maker, $account) {

                    $qb = $er->createQueryBuilder('c');

                    $qb->where('c.maker = :maker')
                        ->andWhere(
                            $qb->expr()->orX(
                                'c.account is null',
                                'c.account = :account'
                            )
                        )
                        ->andWhere(
                            $qb->expr()->in('c.status', [
                                Inverter::STATUS_PUBLISHED, Inverter::STATUS_FEATURED
                            ])
                        )
                        ->orderBy('c.model', 'asc')
                        ->setParameters([
                            'maker' => $maker,
                            'account' => $account
                        ]);

                    return $qb;
                }
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'required' => false
            ])
            ->add('quantity', TextType::class);
    }

    /**
     * @param FormInterface $form
     */
    /*private function addComponentField(FormInterface &$form, $serial)
    {
        $form
            ->add('inverter', EntityType::class, [
                'placeholder' => 'select.model',
                'class' => Inverter::class,
                'query_builder' => function (EntityRepository $er) use ($serial) {

                    return $er->createQueryBuilder('c')
                        ->where('c.serial = :serial')
                        ->orderBy('c.model', 'asc')
                        ->setParameter('serial', $serial);
                }
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'required' => false
            ])
            ->add('quantity', TextType::class);
    }*/
}