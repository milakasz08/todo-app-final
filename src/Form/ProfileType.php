<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class ProfileType.
 *
 * Formularz pozwalajacy zalogowanemu uzytkownikowi zmienic wlasny adres
 * e-mail oraz (opcjonalnie) haslo.
 */
class ProfileType extends AbstractType
{
    private const INPUT_CLASS = 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium text-gray-900 placeholder-gray-400';

    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder budowniczy formularza
     * @param array                $options opcje formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'profile.form.email',
                'attr' => ['class' => self::INPUT_CLASS],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'profile.form.password',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => self::INPUT_CLASS,
                    'placeholder' => 'Zostaw puste, aby nie zmieniać hasła',
                ],
                'help' => 'profile.form.password_help',
                'constraints' => [
                    new Length(
                        min: 6,
                        minMessage: 'register.password.too_short',
                        max: 4096,
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
