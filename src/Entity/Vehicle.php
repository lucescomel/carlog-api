<?php
namespace App\Entity;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: VehicleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [new GetCollection(), new Post(), new Get(), new Patch(), new Put(), new Delete()],
    normalizationContext: ['groups' => ['vehicle:read']],
    denormalizationContext: ['groups' => ['vehicle:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial', 'make' => 'partial', 'model' => 'partial', 'plate' => 'partial', 'vin' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['updatedAt' => 'DESC', 'name' => 'ASC'])]
class Vehicle
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    #[Groups(['vehicle:read','record:read'])]
    private ?int $id = null;
    #[ORM\Column(length: 100)] #[Assert\NotBlank]
    #[Groups(['vehicle:read','vehicle:write','record:read'])]
    private string $name = 'Véhicule';
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['vehicle:read','vehicle:write','record:read'])]
    private ?string $make = null;
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['vehicle:read','vehicle:write','record:read'])]
    private ?string $model = null;
    #[ORM\Column(nullable: true)] #[Groups(['vehicle:read','vehicle:write'])]
    private ?int $year = null;
    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['vehicle:read','vehicle:write','record:read'])]
    private ?string $plate = null;
    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['vehicle:read','vehicle:write'])]
    private ?string $vin = null;
    #[ORM\Column(nullable: true)] #[Groups(['vehicle:read','vehicle:write'])]
    private ?int $odometer = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] #[Groups(['vehicle:read'])]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)] #[Groups(['vehicle:read'])]
    private \DateTime $updatedAt;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    // Pas dans vehicle:write — assigné automatiquement par VehicleOwnerListener
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: ServiceRecord::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $records;
    public function __construct()
    {
        $this->records = new ArrayCollection();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = new \DateTime();
    }
    #[ORM\PrePersist] public function onPrePersist(): void
    { $this->createdAt ??= new \DateTimeImmutable(); $this->updatedAt = new \DateTime(); }
    #[ORM\PreUpdate] public function onPreUpdate(): void
    { $this->updatedAt = new \DateTime(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getMake(): ?string { return $this->make; }
    public function setMake(?string $make): self { $this->make = $make; return $this; }
    public function getModel(): ?string { return $this->model; }
    public function setModel(?string $model): self { $this->model = $model; return $this; }
    public function getYear(): ?int { return $this->year; }
    public function setYear(?int $year): self { $this->year = $year; return $this; }
    public function getPlate(): ?string { return $this->plate; }
    public function setPlate(?string $plate): self { $this->plate = $plate; return $this; }
    public function getVin(): ?string { return $this->vin; }
    public function setVin(?string $vin): self { $this->vin = $vin; return $this; }
    public function getOdometer(): ?int { return $this->odometer; }
    public function setOdometer(?int $odometer): self { $this->odometer = $odometer; return $this; }
    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static { $this->owner = $owner; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTime { return $this->updatedAt; }
    /** @return Collection<int, ServiceRecord> */
    public function getRecords(): Collection { return $this->records; }
    public function addRecord(ServiceRecord $record): self {
        if (!$this->records->contains($record)) { $this->records->add($record); $record->setVehicle($this); }
        return $this;
    }
    public function removeRecord(ServiceRecord $record): self {
        if ($this->records->removeElement($record)) { if ($record->getVehicle() === $this) $record->setVehicle(null); }
        return $this;
    }
}
