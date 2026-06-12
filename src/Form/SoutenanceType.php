<?php

namespace App\Form;

use App\Entity\Enseignant;
use App\Entity\Etudiant;
use App\Entity\Salle;
use App\Entity\Soutenance;
use App\Repository\EtudiantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoutenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentEtudiantId = $options['current_etudiant_id'];

        $builder
            ->add('date', DateType::class, [
                'label' => 'Date de soutenance',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('heure', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'html5' => true,
                'with_seconds' => false,
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'label' => 'Étudiant',
                'query_builder' => function (EtudiantRepository $er) use ($currentEtudiantId) {
                    $qb = $er->createQueryBuilder('e')
                        ->leftJoin('e.soutenances', 's')
                        ->where('s.id IS NULL');

                    if ($currentEtudiantId !== null) {
                        $qb->orWhere('e.id = :cid')->setParameter('cid', $currentEtudiantId);
                    }

                    return $qb->orderBy('e.nom', 'ASC');
                },
                'choice_label' => fn(Etudiant $e) => $e->getNom() . ' ' . $e->getPrenom() . ' — ' . $e->getFiliere(),
                'placeholder' => '-- Sélectionner un étudiant --',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'label' => 'Salle',
                'choice_label' => fn(Salle $s) => $s->getCode() . ' (' . $s->getLocalisation() . ', ' . $s->getCapacite() . ' places)',
                'placeholder' => '-- Sélectionner une salle --',
            ])
            ->add('president', EntityType::class, [
                'class' => Enseignant::class,
                'label' => 'Président du jury',
                'choice_label' => fn(Enseignant $e) => $e->getNom() . ' ' . $e->getPrenom() . ' — ' . $e->getSpecialite(),
                'placeholder' => '-- Sélectionner le président --',
            ])
            ->add('examinateur', EntityType::class, [
                'class' => Enseignant::class,
                'label' => 'Examinateur',
                'choice_label' => fn(Enseignant $e) => $e->getNom() . ' ' . $e->getPrenom() . ' — ' . $e->getSpecialite(),
                'placeholder' => '-- Sélectionner l\'examinateur --',
            ])
            ->add('encadreur', EntityType::class, [
                'class' => Enseignant::class,
                'label' => 'Encadreur',
                'choice_label' => fn(Enseignant $e) => $e->getNom() . ' ' . $e->getPrenom() . ' — ' . $e->getSpecialite(),
                'placeholder' => '-- Sélectionner l\'encadreur --',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Soutenance::class,
            'current_etudiant_id' => null,
        ]);

        $resolver->setAllowedTypes('current_etudiant_id', ['null', 'int']);
    }
}
