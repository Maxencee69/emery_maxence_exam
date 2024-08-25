<?php

namespace App\Form\DataTransformer;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BrandToStringTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $brandRepository;

    public function __construct(EntityManagerInterface $entityManager, BrandRepository $brandRepository)
    {
        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
    }

    public function transform($brand): string
    {
        if (null === $brand) {
            return '';
        }

        return $brand->getName();
    }

    public function reverseTransform($brandName): ?Brand
    {
        if (!$brandName) {
            return null;
        }

        $brand = $this->brandRepository->findOneBy(['name' => $brandName]);

        if (!$brand) {
            $brand = new Brand();
            $brand->setName($brandName);
            $this->entityManager->persist($brand);
            $this->entityManager->flush();
        }

        return $brand;
    }
}
