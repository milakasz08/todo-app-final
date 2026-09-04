<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Resource;
use App\Entity\Tag;
use App\Enum\MediaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
     * @param FormBuilderInterface $builder budowniczy formularza
     * @param array                $options opcje formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900';

        $builder
            ->add('Title', TextType::class, [
                'label' => 'resource.form.title',
                'attr' => ['class' => $inputClass],
            ])
            ->add('Author', TextType::class, [
                'label' => 'resource.form.author',
                'attr' => ['class' => $inputClass],
            ])
            ->add('Type', EnumType::class, [
                'label' => 'resource.form.type',
                'class' => MediaType::class,
                'choice_label' => static fn (MediaType $type): string => $type->label(),
                'placeholder' => 'resource.form.choose_type',
                'attr' => ['class' => $inputClass],
            ])
            ->add('Quantity', IntegerType::class, [
                'label' => 'resource.form.quantity',
                'attr' => ['class' => $inputClass, 'min' => 0],
                'empty_data' => null,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'resource.form.category',
                'placeholder' => 'resource.form.choose_category',
                'required' => true,
                'attr' => ['class' => $inputClass],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'resource.form.tags',
                'required' => false,
            ])
        ;
    }

    /**
     * Configure the form options.
     *
     * @param OptionsResolver $resolver konfigurator opcji formularza
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
