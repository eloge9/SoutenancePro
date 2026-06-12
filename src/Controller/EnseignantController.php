<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\User;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/enseignants')]
#[IsGranted('ROLE_ADMIN')]
class EnseignantController extends AbstractController
{
    #[Route('', name: 'admin_enseignant_index')]
    public function index(EnseignantRepository $repo): Response
    {
        return $this->render('admin/enseignant/index.html.twig', [
            'enseignants' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_enseignant_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier unicité email
            $existingEns = $em->getRepository(Enseignant::class)->findOneBy(['email' => $enseignant->getEmail()]);
            if ($existingEns !== null) {
                $this->addFlash('error', 'Un enseignant avec cet e-mail existe déjà.');
                return $this->render('admin/enseignant/new.html.twig', ['form' => $form]);
            }

            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $enseignant->getEmail()]);
            if ($existingUser !== null) {
                $this->addFlash('error', 'Un compte utilisateur avec cet e-mail existe déjà.');
                return $this->render('admin/enseignant/new.html.twig', ['form' => $form]);
            }

            // Créer le compte User
            $plainPassword = $form->get('plainPassword')->getData();
            $user = new User();
            $user->setEmail($enseignant->getEmail());
            $user->setRoles(['ROLE_ENSEIGNANT']);
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $em->persist($user);

            $enseignant->setCompte($user);
            $em->persist($enseignant);
            $em->flush();

            $this->addFlash('success', 'Enseignant ' . $enseignant->getNom() . ' ' . $enseignant->getPrenom() . ' créé avec succès.');
            return $this->redirectToRoute('admin_enseignant_index');
        }

        return $this->render('admin/enseignant/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'admin_enseignant_show', requirements: ['id' => '\d+'])]
    public function show(Enseignant $enseignant): Response
    {
        return $this->render('admin/enseignant/show.html.twig', ['enseignant' => $enseignant]);
    }

    #[Route('/{id}/edit', name: 'admin_enseignant_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        Enseignant $enseignant,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(EnseignantType::class, $enseignant, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $enseignant->getCompte();

            // Mettre à jour l'email du compte si changé
            if ($user->getEmail() !== $enseignant->getEmail()) {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $enseignant->getEmail()]);
                if ($existingUser !== null && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Un compte avec cet e-mail existe déjà.');
                    return $this->render('admin/enseignant/edit.html.twig', ['form' => $form, 'enseignant' => $enseignant]);
                }
                $user->setEmail($enseignant->getEmail());
            }

            // Mettre à jour le mot de passe si fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Enseignant modifié avec succès.');
            return $this->redirectToRoute('admin_enseignant_show', ['id' => $enseignant->getId()]);
        }

        return $this->render('admin/enseignant/edit.html.twig', ['form' => $form, 'enseignant' => $enseignant]);
    }

    #[Route('/{id}/delete', name: 'admin_enseignant_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Enseignant $enseignant, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $enseignant->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_enseignant_index');
        }

        $hasSoutenances = $enseignant->getSoutenancesPresident()->count() > 0
            || $enseignant->getSoutenancesExaminateur()->count() > 0
            || $enseignant->getSoutenancesEncadreur()->count() > 0;

        if ($hasSoutenances) {
            $this->addFlash('error', 'Impossible de supprimer cet enseignant : il est membre d\'un jury de soutenance.');
            return $this->redirectToRoute('admin_enseignant_index');
        }

        $user = $enseignant->getCompte();

        // Supprimer enseignant puis son compte user
        $em->remove($enseignant);
        $em->flush();

        if ($user !== null) {
            $em->remove($user);
            $em->flush();
        }

        $this->addFlash('success', 'Enseignant et son compte supprimés.');
        return $this->redirectToRoute('admin_enseignant_index');
    }
}
