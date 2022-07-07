<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Town::class, inversedBy="cities")
     * @Ignore()
     */
 
    private $town;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="city")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Shops::class, mappedBy="city")
     */
    private $shops;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->shops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTown(): ?Town
    {
        return $this->town;
    }

    public function setTown(?Town $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Products[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCity($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCity() === $this) {
                $product->setCity(null);
            }
        }

        return $this;
    }


    public function __toString(){
        return $this->name;
    }

    /**
     * @return Collection|Shops[]
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shops $shop): self
    {
        if (!$this->shops->contains($shop)) {
            $this->shops[] = $shop;
            $shop->setCity($this);
        }

        return $this;
    }

    public function removeShop(Shops $shop): self
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getCity() === $this) {
                $shop->setCity(null);
            }
        }

        return $this;
    }
}
