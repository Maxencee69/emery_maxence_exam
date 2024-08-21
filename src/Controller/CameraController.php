<?php

namespace App\Controller;

use App\Repository\CameraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CameraController extends AbstractController
{
    #[Route('/appareils-photo', name: 'appareils_photo_list')]
    public function index(CameraRepository $cameraRepository): Response
    {
        // Récupération de tous les appareils photo depuis le repository
        $cameras = $cameraRepository->findAll();

        // Rendu du template avec la liste des appareils photo
        return $this->render('camera/cameraList.html.twig', [
            'cameras' => $cameras,
        ]);
    }

    #[Route('/camera/{id}', name: 'app_camera_show')]
    public function item(int $id, CameraRepository $cameraRepository): Response
    {
        // Récupérer l'appareil photo spécifique par son ID
        $camera = $cameraRepository->find($id);

        // Vérifier si l'appareil photo existe
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
}
