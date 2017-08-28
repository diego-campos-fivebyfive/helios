<?php

namespace AppBundle\Form\Project;

use AppBundle\Entity\Project\NasaCatalog;
use AppBundle\Form\Project\Helper\NasaMonthType;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NasaCatalogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($this->buildMonths($builder));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NasaCatalog::class
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return FormBuilderInterface
     */
    private function buildMonths(FormBuilderInterface $builder)
    {
        $months = $builder->create('months', FormType::class, []);

        for ($month = 1; $month <= 12; $month++) {
            $months->add($month, TextType::class, []);
        }

        return $months;
    }
}
