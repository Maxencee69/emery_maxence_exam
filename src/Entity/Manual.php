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

    #[ORM\OneToOne(inversedBy: 'manual', targetEntity: Camera::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Camera $camera = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manualPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $format = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCamera(): ?Camera
    {
        return $this->camera;
    }

    public function setCamera(?Camera $camera): static
    {
        $this->camera = $camera;

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
