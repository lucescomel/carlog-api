<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\ServiceRecord;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Filtre les ServiceRecord en joignant sur vehicle.owner = utilisateur courant.
 * Empêche un utilisateur d'accéder aux interventions des véhicules d'un autre.
 */
final class ServiceRecordOwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (ServiceRecord::class !== $resourceClass) {
            return;
        }
        $this->addOwnerFilter($queryBuilder, $queryNameGenerator);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (ServiceRecord::class !== $resourceClass) {
            return;
        }
        $this->addOwnerFilter($queryBuilder, $queryNameGenerator);
    }

    private function addOwnerFilter(QueryBuilder $qb, QueryNameGeneratorInterface $qng): void
    {
        $user = $this->security->getUser();
        $rootAlias = $qb->getRootAliases()[0];

        if (null === $user) {
            $qb->andWhere('1 = 0');
            return;
        }

        // JOIN service_record → vehicle → owner
        $vehicleAlias = $qng->generateJoinAlias('vehicle');
        $qb->join(sprintf('%s.vehicle', $rootAlias), $vehicleAlias)
            ->andWhere(sprintf('%s.owner = :current_owner', $vehicleAlias))
            ->setParameter('current_owner', $user);
    }
}
