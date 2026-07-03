<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType.
 */
class CategoryType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder budowniczy formularza
     * @param array                $options opcje formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', null, [
                'label' => 'category.form.name',
                'attr' => ['class' => 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900'],
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
            'data_class' => Category::class,
        ]);
    }
}
