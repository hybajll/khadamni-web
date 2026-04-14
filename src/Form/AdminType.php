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
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role admin',
                'choices' => [
                    'Super Admin' => Admin::BUSINESS_ROLE_SUPER_ADMIN,
                    'Gestionnaire' => Admin::BUSINESS_ROLE_GESTIONNAIRE,
                    'Moderateur' => Admin::BUSINESS_ROLE_MODERATEUR,
                ],
                'placeholder' => 'Choisir un role',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $isEdit ? 'Nouveau mot de passe' : 'Mot de passe',
                'mapped' => false,
                'required' => !$isEdit,
                'empty_data' => '',
                'help' => $isEdit ? 'Laissez vide pour conserver le mot de passe actuel.' : null,
                'constraints' => $passwordConstraints,
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
