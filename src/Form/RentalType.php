<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\Rental;
use App\Entity\Resource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RentalType.
 */
class RentalType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder budowniczy formularza.
     * @param array                $options opcje formularza.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('resource', EntityType::class, [
                'class' => Resource::class,
                'choice_label' => 'Title',
                'label' => 'Zasób do wypożyczenia',
                'attr' => ['class' => 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Ilość sztuk',
                'data' => 1, // Domyślnie 1 sztuka
                'attr' => [
                    'min' => 1,
                    'class' => 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900',
                ],
            ])
        ;
    }

    /**
     * Configure the form options.
     *
     * @param OptionsResolver $resolver konfigurator opcji formularza.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
