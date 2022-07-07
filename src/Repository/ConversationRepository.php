<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    
 

    /**
     * @return Collection|Conversation[]
     */
    public function findByRelatedTo(User $user) 
    {
        $conversations = $this->findAll();

        $userConversations =  array();


        for ($i=0; $i < sizeof($conversations); $i++) { 
            $tmp = $conversations[$i]->getRelatedTo();

            for ($j=0; $j < sizeof($tmp) ; $j++) { 
                $userTMP = $tmp[$j];
                if ($userTMP->getId() == $user->getId() ) {
                    array_push($userConversations,$tmp);

                }
            }
        }

        return $userConversations;
    } 
}
