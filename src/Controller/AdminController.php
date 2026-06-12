<?php

namespace App\Controller;

use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\SalleRepository;
use App\Repository\SoutenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(
        EtudiantRepository $etudiantRepo,
        EnseignantRepository $enseignantRepo,
        SalleRepository $salleRepo,
        SoutenanceRepository $soutenanceRepo
    ): Response {
        $derniersSoutenances = array_slice(
            $soutenanceRepo->findAllOrderedByDate(),
            0,
            10
        );

        return $this->render('admin/dashboard.html.twig', [
            'nbEtudiants'        => count($etudiantRepo->findAll()),
            'nbEnseignants'      => count($enseignantRepo->findAll()),
            'nbSalles'           => count($salleRepo->findAll()),
            'nbSoutenances'      => count($soutenanceRepo->findAll()),
            'derniersSoutenances' => $derniersSoutenances,
        ]);
    }
}
