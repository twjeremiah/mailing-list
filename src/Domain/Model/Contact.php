<?php

namespace App\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;

class Contact
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $emailAddress,
        private readonly string $name,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toDto(): array
    {
        return [
            'id' => $this->id,
            'email_address' => $this->emailAddress,
            'name' => $this->name,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}