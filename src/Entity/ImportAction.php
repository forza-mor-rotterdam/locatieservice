<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImportActionRepository;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ImportActionRepository::class)]
#[ORM\Table]
#[ORM\Index(name: 'idx_import_action_type', fields: ['type'])]
#[ORM\Index(name: 'idx_import_action_type_start', fields: ['type', 'start'])]
class ImportAction
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[ORM\GeneratedValue('IDENTITY')]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 25, nullable: false)]
    private string $type;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $start;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $finish = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $success = false;

    public function __construct(string $type)
    {
        $this->setType($type);
        $this->setStart(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeImmutable
    {
        return $this->finish;
    }

    public function setFinish(?\DateTimeImmutable $finish): static
    {
        $this->finish = $finish;

        return $this;
    }

    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(?bool $success): static
    {
        $this->success = $success;

        return $this;
    }
}