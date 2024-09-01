<?php

namespace App\Controller;

use App\Entity\Brand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/brand', name: 'app_brand')]
    public function index(): Response
    {
        $brandRepository = $this->entityManager->getRepository(Brand::class);
        $brands = $brandRepository->findBy([], ['name' => 'ASC']);

        // Supprimer les marques sans caméras associées
        foreach ($brands as $brand) {
            if ($brand->getCameras()->isEmpty()) {
                $this->entityManager->remove($brand);
            }
        }
        $this->entityManager->flush(); // Effectuer les suppressions en base de données

        // Récupérer à nouveau les marques après suppression
        $brands = $brandRepository->findBy([], ['name' => 'ASC']);

        return $this->render('brand/camera.html.twig', [
            'brands' => $brands,
        ]);
    }
}
