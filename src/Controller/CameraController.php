<?php

namespace App\Controller;

use App\Entity\Camera;
use App\Entity\Brand;
use App\Form\CameraType;
use App\Repository\BrandRepository;
use App\Repository\CameraRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CameraController extends AbstractController
{
    #[Route('/appareils-photo', name: 'appareils_photo_list')]
    public function index(CameraRepository $cameraRepository): Response
    {
        $cameras = $cameraRepository->findAll();

        return $this->render('camera/cameraList.html.twig', [
            'cameras' => $cameras,
        ]);
    }

    #[Route('/camera/new', name: 'camera_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, BrandRepository $brandRepository): Response
    {
        $camera = new Camera();
        $form = $this->createForm(CameraType::class, $camera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la marque
            $brand = $form->get('brand')->getData();
            $newBrandName = $form->get('newBrand')->getData();

            if ($newBrandName) {
                $brand = new Brand();
                $brand->setName($newBrandName);
                $entityManager->persist($brand);
            }

            // Associer la caméra à la marque
            $camera->setBrand($brand);

            // Gestion des fichiers photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_photos',
                    $newFilename
                );
                $camera->setPhotoPath('/camera_photos/' . $newFilename);
            }

            // Gestion des fichiers manuels
            $manualFile = $form->get('manual')->getData();
            if ($manualFile) {
                $newFilename = uniqid().'.'.$manualFile->guessExtension();
                $manualFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_manuels',
                    $newFilename
                );
                $camera->setManualPath('/camera_manuels/' . $newFilename);
            }

            // Debugging
            dump($camera);
            dump($camera->getBrand());
            dd($form->getErrors(true, false));

            // Persist de la caméra avec la marque associée
            $entityManager->persist($camera);
            $entityManager->flush();

            return $this->redirectToRoute('appareils_photo_list');
        }

        return $this->render('camera/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/camera/{id}', name: 'app_camera_show', requirements: ['id' => '\d+'])]
    public function item(int $id, CameraRepository $cameraRepository): Response
    {
        $camera = $cameraRepository->find($id);

        if (!$camera) {
            throw $this->createNotFoundException('L\'appareil photo demandé n\'existe pas.');
        }

        $manualFilePath = $this->getParameter('kernel.project_dir') . '/public/camera_manuels/' . strtolower($camera->getModelName()) . 'manual.pdf';

        if (!file_exists($manualFilePath)) {
            $manualFilePath = null;
        }

        return $this->render('camera/item.html.twig', [
            'camera' => $camera,
            'manualFileExists' => $manualFilePath !== null
        ]);
    }

    #[Route('/camera/{id}/edit', name: 'camera_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Camera $camera, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CameraType::class, $camera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des fichiers photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_photos',
                    $newFilename
                );
                $camera->setPhotoPath('/camera_photos/' . $newFilename);
            }

            // Gestion des fichiers manuels
            $manualFile = $form->get('manual')->getData();
            if ($manualFile) {
                $newFilename = uniqid().'.'.$manualFile->guessExtension();
                $manualFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_manuels',
                    $newFilename
                );
                $camera->setManualPath('/camera_manuels/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('appareils_photo_list');
        }

        return $this->render('camera/edit.html.twig', [
            'form' => $form->createView(),
            'camera' => $camera,
        ]);
    }

    #[Route('/camera/{id}/delete', name: 'camera_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Camera $camera, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$camera->getId(), $request->request->get('_token'))) {
            $entityManager->remove($camera);
            $entityManager->flush();
        }

        return $this->redirectToRoute('appareils_photo_list');
    }
}
