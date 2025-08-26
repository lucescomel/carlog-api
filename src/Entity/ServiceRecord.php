<?php
namespace App\Entity;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
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
use App\Repository\ServiceRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ServiceRecordRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [new GetCollection(), new Post(), new Get(), new Patch(), new Put(), new Delete()],
    normalizationContext: ['groups' => ['record:read']],
    denormalizationContext: ['groups' => ['record:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['type' => 'partial', 'customType' => 'partial', 'vehicle.plate' => 'partial'])]
#[ApiFilter(DateFilter::class, properties: ['date', 'nextDueDate'])]
#[ApiFilter(NumericFilter::class, properties: ['mileage', 'cost', 'nextDueMileage'])]
#[ApiFilter(OrderFilter::class, properties: ['date' => 'DESC','mileage' => 'DESC'])]
class ServiceRecord
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    #[Groups(['record:read'])]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['record:read','record:write'])]
    private ?Vehicle $vehicle = null;
    #[ORM\Column(length: 32)] #[Assert\NotBlank]
    #[Groups(['record:read','record:write'])]
    private string $type = 'Autre';
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['record:read','record:write'])]
    private ?string $customType = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['record:read','record:write'])]
    private \DateTime $date;
    #[ORM\Column] #[Groups(['record:read','record:write'])]
    private int $mileage = 0;
    #[ORM\Column(nullable: true)] #[Groups(['record:read','record:write'])]
    private ?int $cost = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['record:read','record:write'])]
    private ?string $notes = null;
    #[ORM\Column(nullable: true)] #[Groups(['record:read','record:write'])]
    private ?int $nextDueMileage = null;
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['record:read','record:write'])]
    private ?\DateTime $nextDueDate = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] #[Groups(['record:read'])]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)] #[Groups(['record:read'])]
    private \DateTime $updatedAt;
    #[ORM\PrePersist] public function onPrePersist(): void
    { $now = new \DateTimeImmutable(); $this->createdAt = $now; $this->updatedAt = new \DateTime(); $this->date ??= new \DateTime(); }
    #[ORM\PreUpdate] public function onPreUpdate(): void
    { $this->updatedAt = new \DateTime(); }
    public function getId(): ?int { return $this->id; }
    public function getVehicle(): ?Vehicle { return $this->vehicle; }
    public function setVehicle(?Vehicle $v): self { $this->vehicle = $v; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): self { $this->type = $t; return $this; }
    public function getCustomType(): ?string { return $this->customType; }
    public function setCustomType(?string $ct): self { $this->customType = $ct; return $this; }
    public function getDate(): \DateTime { return $this->date; }
    public function setDate(\DateTime $d): self { $this->date = $d; return $this; }
    public function getMileage(): int { return $this->mileage; }
    public function setMileage(int $m): self { $this->mileage = $m; return $this; }
    public function getCost(): ?int { return $this->cost; }
    public function setCost(?int $c): self { $this->cost = $c; return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $n): self { $this->notes = $n; return $this; }
    public function getNextDueMileage(): ?int { return $this->nextDueMileage; }
    public function setNextDueMileage(?int $v): self { $this->nextDueMileage = $v; return $this; }
    public function getNextDueDate(): ?\DateTime { return $this->nextDueDate; }
    public function setNextDueDate(?\DateTime $d): self { $this->nextDueDate = $d; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTime { return $this->updatedAt; }
}
