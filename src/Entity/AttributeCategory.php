<?php

namespace App\Entity;

use App\Repository\AttributeCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributeCategoryRepository::class)
 */
class AttributeCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Attributes::class, inversedBy="attributeCategories")
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="attributeCategories")
     */
    private $category;


    public function getId(): ?int
    {
        return $this->id;
    }


    
    public function getAttribute(): ?Attributes
    {
        return $this->attribute;
    }

    public function setAttribute(?Attributes $attribute ): self
    {
        $this->attribute = $attribute;

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

 
}
