<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CameraRepository;

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

    #[Route('/camera/{id}', name: 'camera_item')]
    public function item(int $id, CameraRepository $cameraRepository): Response
    {
        // Récupérer l'appareil photo spécifique par son ID
        $camera = $cameraRepository->find($id);

        // Vérifier si l'appareil photo existe
        if (!$camera) {
            throw $this->createNotFoundException('L\'appareil photo demandé n\'existe pas.');
        }

        // Rendu du template avec les détails de l'appareil photo
        return $this->render('camera/item.html.twig', [
            'camera' => $camera,
        ]);
    }
}