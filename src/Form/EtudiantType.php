<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank(['message' => 'Le nom est obligatoire'])],
                'attr' => ['placeholder' => 'Nom de l\'étudiant'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new NotBlank(['message' => 'Le prénom est obligatoire'])],
                'attr' => ['placeholder' => 'Prénom de l\'étudiant'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['placeholder' => 'email@exemple.com'],
            ])
            ->add('filiere', TextType::class, [
                'label' => 'Filière',
                'constraints' => [new NotBlank(['message' => 'La filière est obligatoire'])],
                'attr' => ['placeholder' => 'ex: Informatique, Génie Logiciel...'],
            ])
            ->add('themeMemoire', TextType::class, [
                'label' => 'Thème du mémoire',
                'constraints' => [new NotBlank(['message' => 'Le thème du mémoire est obligatoire'])],
                'attr' => ['placeholder' => 'Intitulé complet du thème de recherche'],
            ])
            ->add('fichierMemoire', FileType::class, [
                'label' => 'Fichier mémoire (PDF)',
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => '.pdf'],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide.',
                        'maxSizeMessage' => 'Le fichier est trop volumineux (max 10 Mo).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
