<?php

namespace App\Listener;

use App\Entity\Message;
use App\Entity\Notation;
use App\Entity\Recommandation;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
class DoctrineListener
{
    private TokenStorageInterface $token_storage;

    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        // If caused by DataFixtures generation => Stop event
        if('cli' == php_sapi_name()) {
            return;
        }

        $entity = $args->getObject();

        switch ($entity) {
            case $entity instanceof Notation:
                $entity->setOwnerUser($this->token_storage->getToken()->getUser());
                $entity->setTimestamp(new \DateTimeImmutable());
            break;

            case $entity instanceof Session:
                $entity->setOwnerUser($this->token_storage->getToken()->getUser());
                break;

            case $entity instanceof Recommandation:
                $entity->setSenderUser($this->token_storage->getToken()->getUser());
                $entity->setTimestamp(new \DateTimeImmutable());
                break;

            case $entity instanceof Message:
                $entity->setSenderUser($this->token_storage->getToken()->getUser());
                $entity->setTimestamp(new \DateTimeImmutable());
                break;
        }
    }
}
