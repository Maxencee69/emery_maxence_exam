<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Camera::class)]
    private Collection $cameras;

    public function __construct()
    {
        $this->cameras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Camera>
     */
    public function getCameras(): Collection
    {
        return $this->cameras;
    }

    public function addCamera(Camera $camera): static
    {
        if (!$this->cameras->contains($camera)) {
            $this->cameras->add($camera);
            $camera->setBrand($this);
        }

        return $this;
    }

    public function removeCamera(Camera $camera): static
    {
        if ($this->cameras->removeElement($camera)) {
            
            if ($camera->getBrand() === $this) {
                $camera->setBrand(null);
            }
        }

        return $this;
    }
}
