<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Resource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title')
            ->add('Author')
            ->add('Quantity')
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Opis',
            ])
            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Pobiera ładną nazwę kategorii zamiast ID
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
            // JAWNA I STABILNA OCHRONA CSRF:
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'resource_form_item',
        ]);
    }
}
