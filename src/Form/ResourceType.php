<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Resource;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResourceType.
 */
class ResourceType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'label' => 'Tytuł',
            ])
            ->add('Author', TextType::class, [
                'label' => 'Autor',
            ])
            ->add('Type', ChoiceType::class, [
                'label' => 'Typ',
                'choices' => [
                    'Książka' => 'Książka',
                    'Płyta' => 'Płyta',
                    'Film' => 'Film',
                ],
            ])
            ->add('Quantity', IntegerType::class, [
                'label' => 'Ilość',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Kategoria',
                'placeholder' => 'Wybierz kategorię',
                'required' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Tagi',
                'required' => false,
            ])
        ;
    }

    /**
     * Configure the form options.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
