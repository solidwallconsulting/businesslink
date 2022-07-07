<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainPhoto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $secondPhoto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thirdPhoto;

    /**
     * 
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addDate;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="products")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="relatedProduct")
     */
    private $conversations;

    /**
     * @ORM\OneToMany(targetEntity=ProductAttributesValues::class, mappedBy="product")
     */
    private $productAttributesValues;

    /**
     * @ORM\OneToMany(targetEntity=FavouritsProducts::class, mappedBy="product")
     */
    private $favouritsProducts;

    /**
     * @ORM\ManyToOne(targetEntity=Shops::class, inversedBy="products")
     */
    private $shop;

    public function __construct()
    {
        $this->conversations = new ArrayCollection();
        $this->productAttributesValues = new ArrayCollection();
        $this->favouritsProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMainPhoto(): ?string
    {
        return $this->mainPhoto;
    }

    public function setMainPhoto(string $mainPhoto): self
    {
        $this->mainPhoto = $mainPhoto;

        return $this;
    }

    public function getSecondPhoto(): ?string
    {
        return $this->secondPhoto;
    }

    public function setSecondPhoto(?string $secondPhoto): self
    {
        $this->secondPhoto = $secondPhoto;

        return $this;
    }

    public function getThirdPhoto(): ?string
    {
        return $this->thirdPhoto;
    }

    public function setThirdPhoto(?string $thirdPhoto): self
    {
        $this->thirdPhoto = $thirdPhoto;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    public function setAddDate(\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->setRelatedProduct($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getRelatedProduct() === $this) {
                $conversation->setRelatedProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductAttributesValues[]
     */
    public function getProductAttributesValues(): Collection
    {
        return $this->productAttributesValues;
    }

    public function addProductAttributesValue(ProductAttributesValues $productAttributesValue): self
    {
        if (!$this->productAttributesValues->contains($productAttributesValue)) {
            $this->productAttributesValues[] = $productAttributesValue;
            $productAttributesValue->setProduct($this);
        }

        return $this;
    }

    public function removeProductAttributesValue(ProductAttributesValues $productAttributesValue): self
    {
        if ($this->productAttributesValues->removeElement($productAttributesValue)) {
            // set the owning side to null (unless already changed)
            if ($productAttributesValue->getProduct() === $this) {
                $productAttributesValue->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FavouritsProducts[]
     */
    public function getFavouritsProducts(): Collection
    {
        return $this->favouritsProducts;
    }

    public function addFavouritsProduct(FavouritsProducts $favouritsProduct): self
    {
        if (!$this->favouritsProducts->contains($favouritsProduct)) {
            $this->favouritsProducts[] = $favouritsProduct;
            $favouritsProduct->setProduct($this);
        }

        return $this;
    }

    public function removeFavouritsProduct(FavouritsProducts $favouritsProduct): self
    {
        if ($this->favouritsProducts->removeElement($favouritsProduct)) {
            // set the owning side to null (unless already changed)
            if ($favouritsProduct->getProduct() === $this) {
                $favouritsProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getShop(): ?Shops
    {
        return $this->shop;
    }

    public function setShop(?Shops $shop): self
    {
        $this->shop = $shop;

        return $this;
    }
}
