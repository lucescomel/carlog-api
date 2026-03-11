<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Vehicle;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Filtre automatiquement les Vehicle pour ne retourner que ceux
 * appartenant à l'utilisateur authentifié.
 *
 * S'applique sur GetCollection ET sur Get/Patch/Put/Delete (item)
 * → un utilisateur ne peut pas accéder aux véhicules d'un autre.
 */
final class VehicleOwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (Vehicle::class !== $resourceClass) {
            return;
        }
        $this->addOwnerFilter($queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (Vehicle::class !== $resourceClass) {
            return;
        }
        $this->addOwnerFilter($queryBuilder);
    }

    private function addOwnerFilter(QueryBuilder $qb): void
    {
        $user = $this->security->getUser();
        $rootAlias = $qb->getRootAliases()[0];

        if (null === $user) {
            // Aucun utilisateur → aucun résultat (ne devrait pas arriver grâce à access_control)
            $qb->andWhere('1 = 0');
            return;
        }

        $qb->andWhere(sprintf('%s.owner = :current_owner', $rootAlias))
            ->setParameter('current_owner', $user);
    }
}
