<?php

namespace App\Entity;

use App\Repository\ShopsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShopsRepository::class)
 */
class Shops
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

 

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoURL;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fixPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $whatsAppNumber;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shops")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Town::class, inversedBy="shops")
     */
    private $town;

 

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shopBanner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $categories = [];

    /**
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="shop")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

 

 
 

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
 

    public function getLogoURL(): ?string
    {
        return $this->logoURL;
    }

    public function setLogoURL(?string $logoURL): self
    {
        $this->logoURL = $logoURL;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFixPhone(): ?string
    {
        return $this->fixPhone;
    }

    public function setFixPhone(?string $fixPhone): self
    {
        $this->fixPhone = $fixPhone;

        return $this;
    }

    public function getWhatsAppNumber(): ?string
    {
        return $this->whatsAppNumber;
    }

    public function setWhatsAppNumber(?string $whatsAppNumber): self
    {
        $this->whatsAppNumber = $whatsAppNumber;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
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
 

    public function getShopBanner(): ?string
    {
        return $this->shopBanner;
    }

    public function setShopBanner(string $shopBanner): self
    {
        $this->shopBanner = $shopBanner;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

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
            $product->setShop($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }

        return $this;
    }


 
    public function __toString(){
        return $this->label;
    }
 
    




}
