<?php

namespace App\Domain;

use App\Domain\Model\Contact;

interface ContactRepository
{
    /** @return Contact[] */
    public function list(): array;

    public function create(string $email, string $name): Contact;

    public function delete(int $id): bool;

    public function loadByEmail(string $email): ?Contact;
}