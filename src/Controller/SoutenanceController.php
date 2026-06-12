<?php

namespace App\Controller;

use App\Entity\Soutenance;
use App\Form\SoutenanceType;
use App\Repository\SoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/soutenances')]
#[IsGranted('ROLE_ADMIN')]
class SoutenanceController extends AbstractController
{
    #[Route('', name: 'admin_soutenance_index')]
    public function index(Request $request, SoutenanceRepository $repo): Response
    {
        $dateSearch = $request->query->get('date', '');
        $soutenances = $dateSearch
            ? $repo->findByDateString($dateSearch)
            : $repo->findAllOrderedByDate();

        return $this->render('admin/soutenance/index.html.twig', [
            'soutenances' => $soutenances,
            'dateSearch'  => $dateSearch,
        ]);
    }

    #[Route('/new', name: 'admin_soutenance_new')]
    public function new(Request $request, EntityManagerInterface $em, SoutenanceRepository $repo): Response
    {
        $soutenance = new Soutenance();
        $form = $this->createForm(SoutenanceType::class, $soutenance, ['current_etudiant_id' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->validerSoutenance($soutenance, $repo, null);
            if ($error) {
                $this->addFlash('error', $error);
                return $this->render('admin/soutenance/new.html.twig', ['form' => $form]);
            }

            $em->persist($soutenance);
            $em->flush();

            $this->addFlash('success', 'Soutenance programmée avec succès.');
            return $this->redirectToRoute('admin_soutenance_show', ['id' => $soutenance->getId()]);
        }

        return $this->render('admin/soutenance/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'admin_soutenance_show', requirements: ['id' => '\d+'])]
    public function show(Soutenance $soutenance): Response
    {
        return $this->render('admin/soutenance/show.html.twig', ['soutenance' => $soutenance]);
    }

    #[Route('/{id}/edit', name: 'admin_soutenance_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Soutenance $soutenance, EntityManagerInterface $em, SoutenanceRepository $repo): Response
    {
        $currentEtudiantId = $soutenance->getEtudiant()?->getId();

        $form = $this->createForm(SoutenanceType::class, $soutenance, [
            'current_etudiant_id' => $currentEtudiantId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->validerSoutenance($soutenance, $repo, $soutenance->getId());
            if ($error) {
                $this->addFlash('error', $error);
                return $this->render('admin/soutenance/edit.html.twig', [
                    'form'       => $form,
                    'soutenance' => $soutenance,
                ]);
            }

            $em->flush();

            $this->addFlash('success', 'Soutenance modifiée avec succès.');
            return $this->redirectToRoute('admin_soutenance_show', ['id' => $soutenance->getId()]);
        }

        return $this->render('admin/soutenance/edit.html.twig', [
            'form'       => $form,
            'soutenance' => $soutenance,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_soutenance_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Soutenance $soutenance, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $soutenance->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_soutenance_index');
        }

        $em->remove($soutenance);
        $em->flush();

        $this->addFlash('success', 'Soutenance annulée et supprimée.');
        return $this->redirectToRoute('admin_soutenance_index');
    }

    private function validerSoutenance(Soutenance $s, SoutenanceRepository $repo, ?int $excludeId): ?string
    {
        $date  = $s->getDate();
        $heure = $s->getHeure();

        // Règle 1 : jury composé de 3 enseignants différents
        $presidentId    = $s->getPresident()?->getId();
        $examinateurId  = $s->getExaminateur()?->getId();
        $encadreurId    = $s->getEncadreur()?->getId();

        if ($presidentId === $examinateurId
            || $presidentId === $encadreurId
            || $examinateurId === $encadreurId
        ) {
            return 'Les 3 membres du jury doivent être des enseignants différents.';
        }

        // Règle 2 : conflit de salle
        $conflitSalle = $repo->findConflitSalle($s->getSalle(), $date, $heure, $excludeId);
        if ($conflitSalle !== null) {
            return 'La salle « ' . $s->getSalle()->getCode() . ' » est déjà occupée à cette date et cette heure.';
        }

        // Règle 3 : conflit jury — vérifier chaque membre séparément
        $membresJury = [
            'Président'   => $s->getPresident(),
            'Examinateur' => $s->getExaminateur(),
            'Encadreur'   => $s->getEncadreur(),
        ];

        foreach ($membresJury as $role => $enseignant) {
            if ($enseignant !== null) {
                $conflit = $repo->findConflitJury($enseignant, $date, $heure, $excludeId);
                if ($conflit !== null) {
                    return $enseignant->getNom() . ' ' . $enseignant->getPrenom()
                        . ' (' . $role . ') est déjà membre d\'un jury à cette date et cette heure.';
                }
            }
        }

        return null;
    }
}
