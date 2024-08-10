<?php

namespace App\Entity;

use App\Repository\CameraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CameraRepository::class)]
class Camera
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $modelName = null;

    #[ORM\Column]
    private ?int $brand_id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $filmFormat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(string $modelName): static
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getBrandId(): ?int
    {
        return $this->brand_id;
    }

    public function setBrandId(int $brand_id): static
    {
        $this->brand_id = $brand_id;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFilmFormat(): ?string
    {
        return $this->filmFormat;
    }

    public function setFilmFormat(string $filmFormat): static
    {
        $this->filmFormat = $filmFormat;

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
