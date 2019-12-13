<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir ce champ.']),
                    new Email(['message' => 'Veuillez indiquer une adresse email.'])
                ]
            ])
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir ce champ']),
                    new Regex([
                          'pattern' => '/^[a-z0-9-_]+$/i',
                          'message' => 'Votre pseudo ne peut contenir que des caractères alphanumériques.'
                    ]),
                    new Length([
                       'min' => 3,
                       'minMessage' => 'Votre pseudo doit contenir au moins 3 caractères.',
                       'max' => 40,
                       'maxMessage' => 'Votre pseudo ne peut contenir plus de 40 caractères.',
                   ])
                ]
            ])
            /*
             * La propriété User::password doit contenir un hash du mot de passe,
             * sauf que le formulaire contiendra le mot de passe original "en clair".
             * Nous créons donc un champ de formulaire qui n'est pas lié à l'entité
             * L'option "mapped" permet cette dissociation
             */
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir ce champ.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins 8 caractères.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
