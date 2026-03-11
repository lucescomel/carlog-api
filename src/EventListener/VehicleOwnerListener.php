<?php

namespace App\EventListener;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Assigne automatiquement l'utilisateur courant comme owner
 * lors de la création d'un Vehicle (PrePersist).
 *
 * Déclenché uniquement à l'INSERT, jamais sur les updates.
 */
#[AsEntityListener(event: Events::prePersist, entity: Vehicle::class)]
class VehicleOwnerListener
{
    public function __construct(private readonly Security $security) {}

    public function prePersist(Vehicle $vehicle, PrePersistEventArgs $args): void
    {
        // N'écraser que si pas déjà défini (sécurité)
        if ($vehicle->getOwner() === null) {
            $user = $this->security->getUser();
            if ($user !== null) {
                $vehicle->setOwner($user); // @phpstan-ignore-line
            }
        }
    }
}
