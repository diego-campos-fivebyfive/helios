<?php

namespace AppBundle\Form\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $element = $options['data'];
        $order = $element->getOrder();
        $manager = $options['manager'];
        $promotional = $options['promocional'];

        $builder
            ->add('code', ChoiceType::class, [
                'choices' => $this->findElements($manager, $promotional, $order, $element->getCode()),
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
            'class' => Element::class,
            'promocional' => null
        ]);

        $resolver->setRequired('manager');
    }

    /**
     * @param \AppBundle\Manager\AbstractManager $manager
     * @param Order $order
     */
    private function findElements($manager, $promotional, Order $order, $code = null)
    {
        $codes = $order->getElements()->map(function (Element $element){
            return $element->getCode();
        })->toArray();

        if ($code) {
            $index = array_search($code, $codes);
            unset($codes[$index]);
        }

        $qb = $manager->createQueryBuilder();

        $aliases = $qb->getRootAliases();

        $qb->where($qb->expr()->notIn(sprintf('%s.code', $aliases[0]), $codes));
        $qb->andWhere($aliases[0].'.status = :status');
        $qb->andWhere($aliases[0].'.available = :available');
        $qb->setParameters([
            'status' => 1,
            'available' => 1
        ]);

        if ($promotional) {
            $qb->andWhere($aliases[0].'.promotional = :promotional');
            $qb->setParameters([
                'status' => 1,
                'available' => 1,
                'promotional' => 1
            ]);
        }


        $components = $qb->getQuery()->getResult();

        $data = [];
        /** @var \AppBundle\Entity\Component\InverterInterface $component */
        foreach ($components as $component){
            $group = 'Outros';
            $code = $component->getCode();

            if(null != $maker = $component->getMaker()){
                $group = $maker->getName();
            }

            $data[$group][$code] = (string) $component;
        }

         return $data;
    }
}
