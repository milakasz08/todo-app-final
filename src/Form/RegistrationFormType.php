<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType.
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder budowniczy formularza
     * @param array                $options opcje formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900 placeholder-gray-400';

        $builder
            ->add('email', null, [
                'label' => 'register.form.email',
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'np. student@uczelnia.pl',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'register.form.password',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => $inputClass,
                    'placeholder' => '••••••••',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'register.password.not_blank',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'register.password.too_short',
                        // Maksymalna długość dopuszczona przez Symfony
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'register.form.agree_terms',
                'mapped' => false,
                'attr' => [
                    'class' => 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-md transition',
                ],
                'constraints' => [
                    new IsTrue(
                        message: 'register.terms.required',
                    ),
                ],
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
            'data_class' => User::class,
        ]);
    }
}
