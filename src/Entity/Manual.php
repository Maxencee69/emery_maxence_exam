<?php

namespace App\Entity;

use App\Repository\ManualRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManualRepository::class)]
class Manual
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Camera $camera_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manualPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $format = null;

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

    public function getManualPath(): ?string
    {
        return $this->manualPath;
    }

    public function setManualPath(?string $manualPath): static
    {
        $this->manualPath = $manualPath;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): static
    {
        $this->format = $format;

        return $this;
    }
}
