<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Camera $camera_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCameraId(): ?Camera
    {
        return $this->camera_id;
    }

    public function setCameraId(?Camera $camera_id): static
    {
        $this->camera_id = $camera_id;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): static
    {
        $this->photoPath = $photoPath;

        return $this;
    }
}
