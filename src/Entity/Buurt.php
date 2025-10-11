<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BuurtRepository;

#[ORM\Entity(repositoryClass: BuurtRepository::class)]
#[ORM\Table()]
#[ORM\Index('idx_buurt_naam', fields: ['naam'])]
#[ORM\UniqueConstraint('uq_buurt_matchnaam', fields: ['matchNaam'])]
#[ORM\Index('idx_buurt_code', fields: ['code'])]
class Buurt extends TopografischeEenheid
{
    #[ORM\ManyToOne(targetEntity: Woonplaats::class, inversedBy: 'buurten')]
    #[ORM\JoinColumn(name: 'woonplaats_uuid', referencedColumnName: 'uuid', nullable: true)]
    private ?Woonplaats $woonplaats = null;

    #[ORM\ManyToOne(targetEntity: Wijk::class, inversedBy: 'buurten')]
    #[ORM\JoinColumn(name: 'wijk_uuid', referencedColumnName: 'uuid', nullable: true)]
    private ?Wijk $wijk = null;

    public function getWoonplaats(): ?Woonplaats
    {
        return $this->woonplaats;
    }

    public function setWoonplaats(?Woonplaats $woonplaats): static
    {
        $this->woonplaats = $woonplaats;

        return $this;
    }

    public function getWijk(): ?Wijk
    {
        return $this->wijk;
    }

    public function setWijk(?Wijk $wijk): static
    {
        $this->wijk = $wijk;

        return $this;
    }
}