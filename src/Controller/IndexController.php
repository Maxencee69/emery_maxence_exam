<?php

namespace App\Controller;

use App\Repository\CameraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(CameraRepository $cameraRepository): Response
    {
        // Récupérer toutes les caméras depuis la base de données
        $cameras = $cameraRepository->findAll();

        // Passer les caméras récupérées au template
        return $this->render('index/index.html.twig', [
            'cameras' => $cameras,
        ]);
    }
}
