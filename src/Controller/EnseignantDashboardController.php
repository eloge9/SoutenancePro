<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Repository\SoutenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/enseignant')]
#[IsGranted('ROLE_ENSEIGNANT')]
class EnseignantDashboardController extends AbstractController
{
    #[Route('', name: 'enseignant_dashboard')]
    public function dashboard(SoutenanceRepository $repo): Response
    {
        $enseignant = $this->getEnseignant();
        if (!$enseignant) {
            return $this->redirectToRoute('app_login');
        }

        $soutenances = $repo->findByEnseignant($enseignant);
        $total       = count($soutenances);

        // Construire la liste avec le rôle de l'enseignant dans chaque soutenance
        $items = $this->buildSoutenanceItems($enseignant, $soutenances);

        // Étudiants distincts concernés
        $etudiantsIds = [];
        foreach ($soutenances as $s) {
            if ($s->getEtudiant()) {
                $etudiantsIds[$s->getEtudiant()->getId()] = true;
            }
        }

        return $this->render('enseignant/dashboard.html.twig', [
            'nbSoutenances' => $total,
            'nbJurys'       => $total,
            'nbEtudiants'   => count($etudiantsIds),
            'soutenances'   => $items,
        ]);
    }

    #[Route('/soutenances', name: 'enseignant_soutenances')]
    public function soutenances(SoutenanceRepository $repo): Response
    {
        $enseignant = $this->getEnseignant();
        if (!$enseignant) {
            return $this->redirectToRoute('app_login');
        }

        $soutenances = $repo->findByEnseignant($enseignant);
        $items = $this->buildSoutenanceItems($enseignant, $soutenances);

        return $this->render('enseignant/soutenances.html.twig', [
            'soutenances' => $items,
        ]);
    }

    #[Route('/jurys', name: 'enseignant_jurys')]
    public function jurys(SoutenanceRepository $repo): Response
    {
        $enseignant = $this->getEnseignant();
        if (!$enseignant) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('enseignant/jurys.html.twig', [
            'soutenancesPresident'   => $enseignant->getSoutenancesPresident()->toArray(),
            'soutenancesExaminateur' => $enseignant->getSoutenancesExaminateur()->toArray(),
            'soutenancesEncadreur'   => $enseignant->getSoutenancesEncadreur()->toArray(),
        ]);
    }

    private function getEnseignant(): ?Enseignant
    {
        $user = $this->getUser();
        return $user?->getEnseignant();
    }

    private function buildSoutenanceItems(Enseignant $enseignant, array $soutenances): array
    {
        $items = [];
        foreach ($soutenances as $soutenance) {
            if ($soutenance->getPresident()?->getId() === $enseignant->getId()) {
                $role = 'Président';
            } elseif ($soutenance->getExaminateur()?->getId() === $enseignant->getId()) {
                $role = 'Examinateur';
            } else {
                $role = 'Encadreur';
            }
            $items[] = ['soutenance' => $soutenance, 'role' => $role];
        }
        return $items;
    }
}
