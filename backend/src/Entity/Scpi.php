<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Scpi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'float')]
    private float $tauxRendementAnnuel;

    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getTauxRendementAnnuel(): float { return $this->tauxRendementAnnuel; }
    public function setTauxRendementAnnuel(float $taux): self { $this->tauxRendementAnnuel = $taux; return $this; }
}
