<?php

namespace App\Controller;

use App\Repository\SoutenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }
        if ($this->isGranted('ROLE_ENSEIGNANT')) {
            return $this->redirectToRoute('enseignant_dashboard');
        }
        return $this->redirectToRoute('public_soutenances');
    }

    #[Route('/soutenances-publiques', name: 'public_soutenances')]
    public function index(Request $request, SoutenanceRepository $repo): Response
    {
        $dateSearch = $request->query->get('date', '');

        if ($dateSearch) {
            try {
                $soutenances = $repo->findByDateString($dateSearch);
            } catch (\Exception) {
                $soutenances = $repo->findAllOrderedByDate();
            }
        } else {
            $soutenances = $repo->findAllOrderedByDate();
        }

        return $this->render('public/soutenances.html.twig', [
            'soutenances' => $soutenances,
            'dateSearch'  => $dateSearch,
        ]);
    }
}
