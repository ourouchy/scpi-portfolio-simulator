<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PortefeuilleEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private float $montant;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Scpi::class)]
    private ?Scpi $scpi = null;

    public function getId(): ?int { return $this->id; }
    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getScpi(): ?Scpi { return $this->scpi; }
    public function setScpi(?Scpi $scpi): self { $this->scpi = $scpi; return $this; }
}
