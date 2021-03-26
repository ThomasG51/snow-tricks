<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'invalid_message' => 'Les mots de passe doivent être identique.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'font-semibold text-blue-400'
                    ],
                    'attr' => [
                        'class' => 'tw-form-field mb-3',
                        'rows' => 14
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'label_attr' => [
                        'class' => 'font-semibold text-blue-400'
                    ],
                    'attr' => [
                        'class' => 'tw-form-field',
                        'rows' => 14
                    ],
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au minimum 8 caractères',
                        'max' => 12,
                        'maxMessage' => 'Votre mot de passe doit contenir au maximum 14 caractères'
                    ]),
                    new Regex([
                        'pattern' => '/[a-z]+/',
                        'message' => 'Le mot de passe doit contenir une minuscule.'
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]+/',
                        'message' => 'Le mot de passe doit contenir une majuscule.'
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]+/',
                        'message' => 'Le mot de passe doit contenir un chiffre.'
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
