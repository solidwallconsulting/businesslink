<?php

namespace App\Entity;
 

 
class ShopAddModel
{ 
    private $id; 
    private $label; 
    private $description; 
    private $city; 
    private $town; 
    
    private $logoURL; 
    private $phone;  
    private $fixPhone; 
    private $whatsAppNumber; 
 

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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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

 

}
