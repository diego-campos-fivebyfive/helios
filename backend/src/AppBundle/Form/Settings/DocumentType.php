<?php

namespace AppBundle\Form\Settings;

use AppBundle\Service\DocumentHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('parameters', FormType::class)
                ->add('type', HiddenType::class)

                // Cover
                ->add('cover_image', FileType::class, [
                    'required' => false,
                    'attr' => [
                        'accept' => 'image/jpeg'
                    ]
                ])

                // Header
                ->add('header_logo', FileType::class, [
                    'required' => false,
                    'attr' => [
                        'accept' => 'image/jpeg,image/png'
                    ]
                ])
                ->add('header_text', TextareaType::class)

                // Theme
                ->add('section_title_background', TextType::class)
                ->add('section_title_color', TextType::class)
                ->add('section_title_font_family', ChoiceType::class, [
                    'choices' => DocumentHelper::getFontFamilies()
                ])
                ->add('section_title_font_size', ChoiceType::class, [
                    'choices' => DocumentHelper::getFontSizes()
                ])
                ->add('chart_color', TextType::class)

                // Sections
                ->add('sections', CollectionType::class, [
                    'entry_type' => DocSectionType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parameter'
        ));
    }
}