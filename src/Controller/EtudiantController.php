<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/etudiants')]
#[IsGranted('ROLE_ADMIN')]
class EtudiantController extends AbstractController
{
    public function __construct(
        #[Autowire('%upload_directory%')]
        private string $uploadDirectory
    ) {}

    #[Route('', name: 'admin_etudiant_index')]
    public function index(Request $request, EtudiantRepository $repo): Response
    {
        $search = $request->query->get('q', '');
        $etudiants = $search
            ? $repo->findBySearch($search)
            : $repo->findAllOrderedByNom();

        return $this->render('admin/etudiant/index.html.twig', [
            'etudiants' => $etudiants,
            'search'    => $search,
        ]);
    }

    #[Route('/new', name: 'admin_etudiant_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichierMemoire')->getData();
            if ($fichier) {
                $newFilename = $this->saveFile($fichier, $slugger);
                $etudiant->setFichierMemoire($newFilename);
            }

            $em->persist($etudiant);
            $em->flush();

            $this->addFlash('success', 'Étudiant ' . $etudiant->getNom() . ' ' . $etudiant->getPrenom() . ' ajouté avec succès.');
            return $this->redirectToRoute('admin_etudiant_index');
        }

        return $this->render('admin/etudiant/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'admin_etudiant_show', requirements: ['id' => '\d+'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('admin/etudiant/show.html.twig', ['etudiant' => $etudiant]);
    }

    #[Route('/{id}/edit', name: 'admin_etudiant_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        Etudiant $etudiant,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichierMemoire')->getData();
            if ($fichier) {
                // Supprimer l'ancien fichier
                if ($etudiant->getFichierMemoire()) {
                    $this->deleteFile($etudiant->getFichierMemoire());
                }
                $newFilename = $this->saveFile($fichier, $slugger);
                $etudiant->setFichierMemoire($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Étudiant modifié avec succès.');
            return $this->redirectToRoute('admin_etudiant_show', ['id' => $etudiant->getId()]);
        }

        return $this->render('admin/etudiant/edit.html.twig', ['form' => $form, 'etudiant' => $etudiant]);
    }

    #[Route('/{id}/delete', name: 'admin_etudiant_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Request $request,
        Etudiant $etudiant,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_etudiant_index');
        }

        if ($etudiant->getFichierMemoire()) {
            $this->deleteFile($etudiant->getFichierMemoire());
        }

        $em->remove($etudiant);
        $em->flush();

        $this->addFlash('success', 'Étudiant supprimé.');
        return $this->redirectToRoute('admin_etudiant_index');
    }

    private function saveFile(mixed $fichier, SluggerInterface $slugger): string
    {
        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }

        $originalName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $slugger->slug($originalName);
        $newFilename = $safeName . '-' . uniqid() . '.pdf';

        $fichier->move($this->uploadDirectory, $newFilename);

        return $newFilename;
    }

    private function deleteFile(string $filename): void
    {
        $filePath = $this->uploadDirectory . '/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
