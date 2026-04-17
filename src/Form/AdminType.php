<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = (bool) $options['is_edit'];

        $passwordConstraints = [
            new Length([
                'min' => 4,
                'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caracteres.',
            ]),
        ];

        if (!$isEdit) {
            $passwordConstraints[] = new NotBlank([
                'message' => 'Le mot de passe est obligatoire.',
            ]);
        }

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom (optionnel)',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prénom (optionnel)',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'admin@exemple.com',
                ],
            ])
            ->add('adminRole', ChoiceType::class, [
                'label' => 'Rôle admin',
                'choices' => [
                    'Super Admin' => Admin::BUSINESS_ROLE_SUPER_ADMIN,
                    'Gestionnaire' => Admin::BUSINESS_ROLE_GESTIONNAIRE,
                    'Modérateur' => Admin::BUSINESS_ROLE_MODERATEUR,
                ],
                'placeholder' => 'Choisir un rôle',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un rôle admin.']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $isEdit ? 'Nouveau mot de passe' : 'Mot de passe',
                'mapped' => false,
                'required' => !$isEdit,
                'empty_data' => '',
                'help' => $isEdit ? 'Laissez vide pour conserver le mot de passe actuel.' : 'Obligatoire (minimum 4 caractères).',
                'constraints' => $passwordConstraints,
                'attr' => [
                    'placeholder' => 'Mot de passe',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
