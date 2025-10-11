<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WoonplaatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WoonplaatsRepository::class)]
#[ORM\Table()]
#[ORM\Index('idx_woonplaats_naam', fields: ['naam'])]
#[ORM\UniqueConstraint('uq_woonplaats_matchnaam', fields: ['matchNaam'])]
#[ORM\Index('idx_woonplaats_code', fields: ['code'])]
class Woonplaats extends TopografischeEenheid
{
    #[ORM\OneToMany(targetEntity: Wijk::class, mappedBy: 'woonplaats')]
    private Collection $wijken;

    #[ORM\OneToMany(targetEntity: Buurt::class, mappedBy: 'woonplaats')]
    private Collection $buurten;

    public function __construct()
    {
        $this->wijken = new ArrayCollection();
        $this->buurten = new ArrayCollection();
    }

    /**
     * @return Collection<int, Wijk>
     */
    public function getWijken(): Collection
    {
        return $this->wijken;
    }

    public function addWijken(Wijk $wijken): static
    {
        if (!$this->wijken->contains($wijken)) {
            $this->wijken->add($wijken);
            $wijken->setWoonplaats($this);
        }

        return $this;
    }

    public function removeWijken(Wijk $wijken): static
    {
        if ($this->wijken->removeElement($wijken)) {
            // set the owning side to null (unless already changed)
            if ($wijken->getWoonplaats() === $this) {
                $wijken->setWoonplaats(null);
            }
        }

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
            $buurten->setWoonplaats($this);
        }

        return $this;
    }

    public function removeBuurten(Buurt $buurten): static
    {
        if ($this->buurten->removeElement($buurten)) {
            // set the owning side to null (unless already changed)
            if ($buurten->getWoonplaats() === $this) {
                $buurten->setWoonplaats(null);
            }
        }

        return $this;
    }
}