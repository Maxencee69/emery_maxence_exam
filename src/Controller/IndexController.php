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
        $cameras = $cameraRepository->findAll();

        return $this->render('index/index.html.twig', [
            'cameras' => $cameras,
        ]);
    }
}
