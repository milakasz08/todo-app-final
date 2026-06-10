<?php

namespace App\Form;

use App\Entity\Rental;
use App\Entity\Resource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Dodany import typu liczbowego
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('borrowerName')
            ->add('quantity', IntegerType::class, [ // Wymuszamy typ Integer
                'label' => 'Ilość sztuk',
                'attr' => [
                    'min' => 1, // Przeglądarka nie pozwoli wpisać mniej niż 1
                    'value' => 1 // Domyślna wartość w polu to 1
                ]
            ])
            ->add('rentedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('returnedAt', null, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('resource', EntityType::class, [
                'class' => Resource::class,
                'choice_label' => 'Title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
