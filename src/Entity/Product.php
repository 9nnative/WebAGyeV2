<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @ORM\Column(type="string")
     */
    private $brochureFilename;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="products")
     */
    private $category;

    public function getBrochureFilename()
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename($brochureFilename)
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    public function getCategory(): ?Album
    {
        return $this->category;
    }

    public function setCategory(?Album $category): self
    {
        $this->category = $category;

        return $this;
    }
}
