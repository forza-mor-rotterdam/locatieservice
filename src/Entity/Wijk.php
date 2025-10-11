<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WijkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WijkRepository::class)]
#[ORM\Table()]
#[ORM\Index('idx_wijk_naam', fields: ['naam'])]
#[ORM\UniqueConstraint('uq_wijk_matchnaam', fields: ['matchNaam'])]
#[ORM\Index('idx_wijk_code', fields: ['code'])]
class Wijk extends TopografischeEenheid
{
    #[ORM\ManyToOne(targetEntity: Woonplaats::class, inversedBy: 'wijken')]
    #[ORM\JoinColumn(name: 'woonplaats_uuid', referencedColumnName: 'uuid', nullable: true)]
    private ?Woonplaats $woonplaats = null;

    #[ORM\OneToMany(targetEntity: Buurt::class, mappedBy: 'wijk')]
    private Collection $buurten;

    public function __construct()
    {
        $this->buurten = new ArrayCollection();
    }

    public function getWoonplaats(): ?Woonplaats
    {
        return $this->woonplaats;
    }

    public function setWoonplaats(?Woonplaats $woonplaats): static
    {
        $this->woonplaats = $woonplaats;

        return $this;
    }

    /**
     * @return Collection<int, Buurt>
     */
    public function getBuurten(): Collection
    {
        return $this->buurten;
    }

    public function addBuurten(Buurt $buurten): static
    {
        if (!$this->buurten->contains($buurten)) {
            $this->buurten->add($buurten);
            $buurten->setWijk($this);
        }

        return $this;
    }

    public function removeBuurten(Buurt $buurten): static
    {
        if ($this->buurten->removeElement($buurten)) {
            // set the owning side to null (unless already changed)
            if ($buurten->getWijk() === $this) {
                $buurten->setWijk(null);
            }
        }

        return $this;
    }
}