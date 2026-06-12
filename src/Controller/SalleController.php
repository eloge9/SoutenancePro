<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/salles')]
#[IsGranted('ROLE_ADMIN')]
class SalleController extends AbstractController
{
    #[Route('', name: 'admin_salle_index')]
    public function index(SalleRepository $repo): Response
    {
        return $this->render('admin/salle/index.html.twig', [
            'salles' => $repo->findAllOrderedByCode(),
        ]);
    }

    #[Route('/new', name: 'admin_salle_new')]
    public function new(Request $request, EntityManagerInterface $em, SalleRepository $repo): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = sprintf('SALLE-%03d', $repo->count([]) + 1);
            $salle->setCode($code);

            $em->persist($salle);
            $em->flush();

            $this->addFlash('success', 'Salle « ' . $salle->getNom() . ' » créée (code : ' . $code . ').');
            return $this->redirectToRoute('admin_salle_index');
        }

        return $this->render('admin/salle/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'admin_salle_show', requirements: ['id' => '\d+'])]
    public function show(Salle $salle): Response
    {
        return $this->render('admin/salle/show.html.twig', ['salle' => $salle]);
    }

    #[Route('/{id}/edit', name: 'admin_salle_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Salle $salle, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Salle modifiée avec succès.');
            return $this->redirectToRoute('admin_salle_show', ['id' => $salle->getId()]);
        }

        return $this->render('admin/salle/edit.html.twig', ['form' => $form, 'salle' => $salle]);
    }

    #[Route('/{id}/delete', name: 'admin_salle_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Salle $salle, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $salle->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_salle_index');
        }

        if ($salle->getSoutenances()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette salle : elle est utilisée dans des soutenances.');
            return $this->redirectToRoute('admin_salle_index');
        }

        $em->remove($salle);
        $em->flush();

        $this->addFlash('success', 'Salle supprimée.');
        return $this->redirectToRoute('admin_salle_index');
    }
}
