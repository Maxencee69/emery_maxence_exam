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
        $cameras = $cameraRepository->findBy([], ['id' => 'DESC']);

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
                $camera->setPhotoPath('/camera_photos/' . $newFilename);
            }

            
            $manualFile = $form->get('manual')->getData();
            if ($manualFile) {
                $newFilename = uniqid().'.'.$manualFile->guessExtension();
                $manualFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_manuels',
                    $newFilename
                );
                $camera->setManualPath('/camera_manuels/' . $newFilename);
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

    #[Route('/camera/{id}', name: 'app_camera_show', requirements: ['id' => '\d+'])]
    public function item(int $id, CameraRepository $cameraRepository): Response
    {
        $camera = $cameraRepository->find($id);

        if (!$camera) {
            throw $this->createNotFoundException("L'appareil photo demandé n'existe pas.");
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
            
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/camera_photos',
                    $newFilename
                );
                $camera->setPhotoPath('/camera_photos/' . $newFilename);
            }

            
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

            
            $this->addFlash('success', 'La caméra a été modifiée avec succès.');

            
            return $this->render('camera/edit.html.twig', [
                'form' => $form->createView(),
                'camera' => $camera,
            ]);
        }

        return $this->render('camera/edit.html.twig', [
            'form' => $form->createView(),
            'camera' => $camera,
        ]);
    }

    #[Route('/camera/{id}/delete', name: 'camera_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Camera $camera, EntityManagerInterface $entityManager, CameraRepository $cameraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$camera->getId(), $request->request->get('_token'))) {
            
            $brand = $camera->getBrand();

            
            $photoPath = $camera->getPhotoPath();
            $manualPath = $camera->getManualPath();

            if ($photoPath && file_exists($this->getParameter('kernel.project_dir').'/public'.$photoPath)) {
                unlink($this->getParameter('kernel.project_dir').'/public'.$photoPath);
            }

            if ($manualPath && file_exists($this->getParameter('kernel.project_dir').'/public'.$manualPath)) {
                unlink($this->getParameter('kernel.project_dir').'/public'.$manualPath);
            }

            
            $entityManager->remove($camera);
            $entityManager->flush();

            
            $remainingCameras = $cameraRepository->findBy(['brand' => $brand]);

            if (empty($remainingCameras)) {
                
                $entityManager->remove($brand);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('appareils_photo_list');
    }
}
