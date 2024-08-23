<?php

namespace App\Entity;

use App\Repository\CameraRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CameraRepository::class)]
class Camera
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $modelName = null;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'cameras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    #[ORM\OneToOne(targetEntity: Manual::class, mappedBy: 'camera', cascade: ['persist', 'remove'])]
    private ?Manual $manual = null;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'camera', cascade: ['persist', 'remove'])]
    private Collection $photos;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: "text")] // Changement du type à text pour les descriptions plus longues
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $filmFormat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manualPath = null; 

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

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

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getManual(): ?Manual
    {
        return $this->manual;
    }

    public function setManual(?Manual $manual): static
    {
        $this->manual = $manual;

        return $this;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setCamera($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            
            if ($photo->getCamera() === $this) {
                $photo->setCamera(null);
            }
        }

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

    public function getManualPath(): ?string 
    {
        return $this->manualPath;
    }

    public function setManualPath(?string $manualPath): static 
    {
        $this->manualPath = $manualPath;

        return $this;
    }
}
