<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="conversations")
     */
    private $relatedProduct;

 

    /**
     * @ORM\OneToMany(targetEntity=ChatMessages::class, mappedBy="conversation")
     */
    private $chatMessages;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="conversations")
     */
    private $relatedTo;

    public function __construct()
    {
         
        $this->chatMessages = new ArrayCollection();
        $this->relatedTo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelatedProduct(): ?Products
    {
        return $this->relatedProduct;
    }

    public function setRelatedProduct(?Products $relatedProduct): self
    {
        $this->relatedProduct = $relatedProduct;

        return $this;
    }

 

    /**
     * @return Collection|ChatMessages[]
     */
    public function getChatMessages(): Collection
    {
        return $this->chatMessages;
    }

    public function addChatMessage(ChatMessages $chatMessage): self
    {
        if (!$this->chatMessages->contains($chatMessage)) {
            $this->chatMessages[] = $chatMessage;
            $chatMessage->setConversation($this);
        }

        return $this;
    }

    public function removeChatMessage(ChatMessages $chatMessage): self
    {
        if ($this->chatMessages->removeElement($chatMessage)) {
            // set the owning side to null (unless already changed)
            if ($chatMessage->getConversation() === $this) {
                $chatMessage->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRelatedTo(): Collection
    {
        return $this->relatedTo;
    }

    public function addRelatedTo(User $relatedTo): self
    {
        if (!$this->relatedTo->contains($relatedTo)) {
            $this->relatedTo[] = $relatedTo;
        }

        return $this;
    }

    public function removeRelatedTo(User $relatedTo): self
    {
        $this->relatedTo->removeElement($relatedTo);

        return $this;
    }
}
