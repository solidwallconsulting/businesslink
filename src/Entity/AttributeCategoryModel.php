<?php

namespace App\Entity; 
 
class AttributeCategoryModel
{
  
    private $attribute;
 
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

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(?int $category): self
    {
        $this->category = $category;

        return $this;
    }

 
}
