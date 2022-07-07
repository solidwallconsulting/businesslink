<?php

namespace App\Entity;

use App\Repository\AttributeValuesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributeValuesRepository::class)
 */
class AttributeValues
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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Attributes::class, inversedBy="attributeValues")
     */
    private $attribute;

    /**
     * @ORM\OneToMany(targetEntity=ProductAttributesValues::class, mappedBy="attribute")
     */
    private $productAttributesValues;

    public function __construct()
    {
        $this->productAttributesValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getAttribute(): ?Attributes
    {
        return $this->attribute;
    }

    public function setAttribute(?Attributes $attribute): self
    {
        $this->attribute = $attribute;

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
            $productAttributesValue->setAttribute($this);
        }

        return $this;
    }

    public function removeProductAttributesValue(ProductAttributesValues $productAttributesValue): self
    {
        if ($this->productAttributesValues->removeElement($productAttributesValue)) {
            // set the owning side to null (unless already changed)
            if ($productAttributesValue->getAttribute() === $this) {
                $productAttributesValue->setAttribute(null);
            }
        }

        return $this;
    }
}
