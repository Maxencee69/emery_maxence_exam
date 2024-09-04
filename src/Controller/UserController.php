<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Camera;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ChangePasswordType;

class UserController extends AbstractController
{
    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $cameras = $entityManager->getRepository(Camera::class)->findBy(['owner' => $user]);
        foreach ($cameras as $camera) {
            $entityManager->remove($camera);
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur et caméras associés supprimés avec succès.');
        }

        return $this->redirectToRoute('admin_cameras');
    }

    #[Route('/user/cameras', name: 'user_cameras')]
    public function userCameras(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $cameras = $entityManager->getRepository(Camera::class)->findBy(['owner' => $user]);

        return $this->render('user/cameras.html.twig', [
            'cameras' => $cameras,
        ]);
    }

    #[Route('/user/delete-account', name: 'user_delete_account', methods: ['POST'])]
    public function deleteAccount(
        Request $request, 
        EntityManagerInterface $entityManager, 
        TokenStorageInterface $tokenStorage
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $cameras = $entityManager->getRepository(Camera::class)->findBy(['owner' => $user]);
        foreach ($cameras as $camera) {
            $entityManager->remove($camera);
        }

        if ($this->isCsrfTokenValid('delete_account'.$user->getId(), $request->request->get('_token'))) {
            
            $entityManager->remove($user);
            $entityManager->flush();

            
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            
            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('error', 'Échec de la suppression du compte.');
        return $this->redirectToRoute('user_profile');
    }

    #[Route('/user/profile', name: 'user_profile')]
    public function userProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
            }
        }

        $cameras = $entityManager->getRepository(Camera::class)->findBy(['owner' => $user]);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'cameras' => $cameras,
        ]);
    }
}
