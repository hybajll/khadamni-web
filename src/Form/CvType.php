<?php

namespace App\Form;

use App\Entity\Cv;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('contenuOriginal', TextareaType::class, [
                'label' => 'Contenu original',
                'attr' => ['rows' => 8],
            ])
            ->add('contenuAmeliore', TextareaType::class, [
                'label' => 'Contenu ameliore',
                'required' => false,
                'attr' => ['rows' => 8],
            ])
            ->add('nombreAmeliorations', IntegerType::class, [
                'label' => 'Nombre d ameliorations',
            ])
            ->add('estPublic', CheckboxType::class, [
                'label' => 'Rendre ce CV public',
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Utilisateur',
                'placeholder' => 'Choisir un utilisateur',
                'choice_label' => static function (User $user): string {
                    return sprintf(
                        '%s %s - %s',
                        $user->getNom(),
                        $user->getPrenom(),
                        $user->getEmail()
                    );
                },
                'query_builder' => static function (UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->andWhere('u INSTANCE OF App\Entity\Etudiant OR u INSTANCE OF App\Entity\Diplome')
                        ->orderBy('u.nom', 'ASC')
                        ->addOrderBy('u.prenom', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cv::class,
        ]);
    }
}
