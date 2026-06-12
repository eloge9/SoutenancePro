<?php

namespace App\Form;

use App\Entity\Salle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la salle',
                'constraints' => [new NotBlank(['message' => 'Le nom est obligatoire'])],
                'attr' => ['placeholder' => 'ex: Amphithéâtre A, Salle de conférence…'],
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité (nombre de places)',
                'constraints' => [
                    new NotBlank(['message' => 'La capacité est obligatoire']),
                    new GreaterThan(['value' => 0, 'message' => 'La capacité doit être supérieure à zéro']),
                ],
                'attr' => ['placeholder' => 'Nombre de places', 'min' => 1],
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'constraints' => [new NotBlank(['message' => 'La localisation est obligatoire'])],
                'attr' => ['placeholder' => 'ex: Bâtiment A, 1er étage'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
