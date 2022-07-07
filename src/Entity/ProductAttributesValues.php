<?php

namespace App\Entity;

use App\Repository\ProductAttributesValuesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductAttributesValuesRepository::class)
 */
class ProductAttributesValues
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="productAttributesValues")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=AttributeValues::class, inversedBy="productAttributesValues")
     */
    private $attribute;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAttribute(): ?AttributeValues
    {
        return $this->attribute;
    }

    public function setAttribute(?AttributeValues $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }
}
