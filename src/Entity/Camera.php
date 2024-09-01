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

    #[ORM\Column(length: 255)]
    private ?string $filmFormat = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'cameras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cameras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'camera', targetEntity: Photo::class, cascade: ['persist', 'remove'])]
    private Collection $photos;

    #[ORM\OneToOne(mappedBy: 'camera', targetEntity: Manual::class, cascade: ['persist', 'remove'])]
    private ?Manual $manual = null;

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

    public function getFilmFormat(): ?string
    {
        return $this->filmFormat;
    }

    public function setFilmFormat(string $filmFormat): static
    {
        $this->filmFormat = $filmFormat;

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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
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

    public function getManual(): ?Manual
    {
        return $this->manual;
    }

    public function setManual(?Manual $manual): static
    {
        if ($manual === null && $this->manual !== null) {
            $this->manual->setCamera(null);
        }

        if ($manual !== null && $manual->getCamera() !== $this) {
            $manual->setCamera($this);
        }

        $this->manual = $manual;

        return $this;
    }
}
