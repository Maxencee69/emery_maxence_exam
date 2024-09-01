<?php

namespace App\Controller;

use App\Entity\Camera;
use App\Entity\Brand;
use App\Entity\Photo;
use App\Entity\Manual;
use App\Form\CameraType;
use App\Repository\BrandRepository;
use App\Repository\CameraRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CameraController extends AbstractController
{
    #[Route('/appareils-photo', name: 'appareils_photo_list')]
    public function index(CameraRepository $cameraRepository): Response
    {
        $cameras = $cameraRepository->findBy([], ['id' => 'DESC']);

        return $this->render('camera/cameraList.html.twig', [
            'cameras' => $cameras,
        ]);
    }

    #[Route('/camera/new', name: 'camera_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager, BrandRepository $brandRepository): Response
    {
        $camera = new Camera();
        $camera->setOwner($this->getUser());

        $form = $this->createForm(CameraType::class, $camera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandName = trim($form->get('brand')->getData());
            $normalizedBrandName = strtolower($brandName);

            $brand = $brandRepository->createQueryBuilder('b')
                ->where('LOWER(TRIM(b.name)) = :brandName')
                ->setParameter('brandName', $normalizedBrandName)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$brand) {
                $brand = new Brand();
                $brand->setName($brandName); 
                $entityManager->persist($brand);
                $entityManager->flush(); 
            }

            $camera->setBrand($brand);

            
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_photos',
                    $newFilename
                );

                $photo = new Photo();
                $photo->setCamera($camera);
                $photo->setPhotoPath('/camera_photos/' . $newFilename);
                $entityManager->persist($photo);
            }

            
            $manualFile = $form->get('manual')->getData();
            if ($manualFile) {
                $newFilename = uniqid().'.'.$manualFile->guessExtension();
                $manualFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_manuels',
                    $newFilename
                );

                $manual = new Manual();
                $manual->setCamera($camera);
                $manual->setManualPath('/camera_manuels/' . $newFilename);
                $entityManager->persist($manual);
            }

            $entityManager->persist($camera);
            $entityManager->flush();

            $this->addFlash('success', 'L\'appareil photo a été ajouté avec succès.');

            return $this->redirectToRoute('camera_new');
        }

        return $this->render('camera/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/camera/{id}', name: 'app_camera_show', requirements: ['id' => '\\d+'])]
    public function camera(int $id, CameraRepository $cameraRepository): Response
    {
        $camera = $cameraRepository->find($id);

        if (!$camera) {
            $this->addFlash('error', 'L\'appareil photo demandé n\'existe pas ou a été supprimé.');
            return $this->redirectToRoute('appareils_photo_list');
        }

        return $this->render('camera/camera.html.twig', [
            'camera' => $camera,
        ]);
    }

    #[Route('/camera/{id}/edit', name: 'camera_edit', requirements: ['id' => '\\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, Camera $camera, EntityManagerInterface $entityManager): Response
    {
        if ($camera->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cette caméra.');
        }

        $form = $this->createForm(CameraType::class, $camera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_photos',
                    $newFilename
                );

                $photo = new Photo();
                $photo->setCamera($camera);
                $photo->setPhotoPath('/camera_photos/' . $newFilename);
                $entityManager->persist($photo);
            }

            
            $manualFile = $form->get('manual')->getData();
            if ($manualFile) {
                $newFilename = uniqid().'.'.$manualFile->guessExtension();
                $manualFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_manuels',
                    $newFilename
                );

                $manual = new Manual();
                $manual->setCamera($camera);
                $manual->setManualPath('/camera_manuels/' . $newFilename);
                $entityManager->persist($manual);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La caméra a été modifiée avec succès.');
        }

        return $this->render('camera/edit.html.twig', [
            'form' => $form->createView(),
            'camera' => $camera,
        ]);
    }

    #[Route('/camera/{id}/delete', name: 'camera_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, Camera $camera, EntityManagerInterface $entityManager, CameraRepository $cameraRepository): Response
    {
        if ($camera->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cette caméra.');
        }

        if ($this->isCsrfTokenValid('delete'.$camera->getId(), $request->request->get('_token'))) {
            $brand = $camera->getBrand();

            $photos = $camera->getPhotos();
            $manual = $camera->getManual();

            
            foreach ($photos as $photo) {
                $photoPath = $photo->getPhotoPath();
                if ($photoPath && file_exists($this->getParameter('kernel.project_dir').'/public'.$photoPath)) {
                    unlink($this->getParameter('kernel.project_dir').'/public'.$photoPath);
                }
                $entityManager->remove($photo);
            }

            
            if ($manual) {
                $manualPath = $manual->getManualPath();
                if ($manualPath && file_exists($this->getParameter('kernel.project_dir').'/public'.$manualPath)) {
                    unlink($this->getParameter('kernel.project_dir').'/public'.$manualPath);
                }
                $entityManager->remove($manual);
            }

            $entityManager->remove($camera);
            $entityManager->flush();

            
            $remainingCameras = $cameraRepository->findBy(['brand' => $brand]);

            
            if (empty($remainingCameras)) {
                $entityManager->remove($brand);
                $entityManager->flush();
            }

            
            $this->addFlash('success', 'L\'appareil a été supprimé avec succès.');

            
            return $this->redirect($request->headers->get('referer'));
        }

        
        $this->addFlash('error', 'Échec de la suppression de l\'appareil.');
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/admin/cameras', name: 'admin_cameras')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminCameras(CameraRepository $cameraRepository, UserRepository $userRepository): Response
    {
        $cameras = $cameraRepository->findAll(); 
        $users = $userRepository->findAll();

        $usersWithoutCameras = [];
        foreach ($users as $user) {
            if ($user->getCameras()->count() === 0) {
                $usersWithoutCameras[] = $user;
            }
        }

        return $this->render('admin/cameras.html.twig', [
            'cameras' => $cameras,
            'users' => $users,
            'usersWithoutCameras' => $usersWithoutCameras,
        ]);
    }
}
