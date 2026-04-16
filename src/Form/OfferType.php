<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du poste',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: Developpeur Senior PHP'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du poste',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Decrivez le poste et les responsabilites',
                    'rows' => 5,
                ],
            ])
            ->add('domain', TextType::class, [
                'label' => 'Domaine',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: Developpement Web'],
            ])
            ->add('salary', NumberType::class, [
                'label' => 'Salaire (en dinars)',
                'required' => false,
                'html5' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00', 'step' => '0.01', 'min' => '0'],
            ])
            ->add('contractType', ChoiceType::class, [
                'label' => 'Type de contrat',
                'required' => true,
                'choices' => [
                    'Stage' => Offer::CONTRACT_INTERNSHIP,
                    'Freelance' => Offer::CONTRACT_FREELANCE,
                    'Temps partiel' => Offer::CONTRACT_PART_TIME,
                    'Temps plein' => Offer::CONTRACT_FULL_TIME,
                ],
                'placeholder' => 'Choisir un type de contrat',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: Tunis, Sousse, Remote'],
            ])
            ->add('experienceLevel', ChoiceType::class, [
                'label' => 'Niveau d\'experience requis',
                'required' => false,
                'choices' => [
                    'Junior' => Offer::LEVEL_JUNIOR,
                    'Mid' => Offer::LEVEL_MID,
                    'Senior' => Offer::LEVEL_SENIOR,
                    'Manager' => Offer::LEVEL_MANAGER,
                ],
                'placeholder' => 'Choisir un niveau',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('expirationDate', DateType::class, [
                'label' => 'Date d\'expiration',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
