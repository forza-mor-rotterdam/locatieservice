<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;

#[ORM\MappedSuperclass()]
abstract class TopografischeEenheid
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $uuid;

    #[ORM\Column(type: Types::STRING, length: 25, nullable: true)]
    private ?string $code;

    #[ORM\Column(type: Types::STRING, length: 75, nullable: true)]
    private ?string $naam;

    #[ORM\Column(type: Types::STRING, length: 75, nullable: true)]
    private ?string $matchNaam;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $geoCenter;

    #[ORM\Column(type: Types::STRING, length: 25, nullable: true)]
    private ?string $gemeenteCode;

    #[ORM\Column(type: Types::STRING, length: 75, nullable: true)]
    private ?string $gemeenteNaam;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private ?bool $inPdok = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSeenInPdok;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private ?bool $inMorCore = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSeenInMorCore;

    static public function createMatchableString(string $input): string
    {
        return trim(mb_strtolower($input));
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getNaam(): ?string
    {
        return $this->naam;
    }

    public function setNaam(?string $naam): static
    {
        $this->naam = $naam;

        $this->updateMatchNaam();

        return $this;
    }

    public function updateMatchNaam(): static
    {
        $this->matchNaam = static::createMatchableString($this->getNaam());

        return $this;
    }

    public function getMatchNaam(): ?string
    {
        return $this->matchNaam;
    }

    public function getGeoCenter(): ?string
    {
        return $this->geoCenter;
    }

    public function setGeoCenter(?string $geoCenter): static
    {
        $this->geoCenter = $geoCenter;

        return $this;
    }

    public function getGemeenteCode(): ?string
    {
        return $this->gemeenteCode;
    }

    public function setGemeenteCode(?string $gemeenteCode): static
    {
        $this->gemeenteCode = $gemeenteCode;

        return $this;
    }

    public function getGemeenteNaam(): ?string
    {
        return $this->gemeenteNaam;
    }

    public function setGemeenteNaam(?string $gemeenteNaam): static
    {
        $this->gemeenteNaam = $gemeenteNaam;

        return $this;
    }

    public function isInPdok(): ?bool
    {
        return $this->inPdok;
    }

    public function setInPdok(bool $inPdok): static
    {
        $this->inPdok = $inPdok;

        return $this;
    }

    public function getLastSeenInPdok(): ?\DateTimeImmutable
    {
        return $this->lastSeenInPdok;
    }

    public function setLastSeenInPdok(\DateTimeImmutable $lastSeenInPdok): static
    {
        $this->lastSeenInPdok = $lastSeenInPdok;

        return $this;
    }

    public function isInMorCore(): ?bool
    {
        return $this->inMorCore;
    }

    public function setInMorCore(bool $inMorCore): static
    {
        $this->inMorCore = $inMorCore;

        return $this;
    }

    public function getLastSeenInMorCore(): ?\DateTimeImmutable
    {
        return $this->lastSeenInMorCore;
    }

    public function setLastSeenInMorCore(\DateTimeImmutable $lastSeenInMorCore): static
    {
        $this->lastSeenInMorCore = $lastSeenInMorCore;

        return $this;
    }
}