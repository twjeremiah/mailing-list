<?php

namespace App\Infrastructure\Repository;

use App\Domain\ContactRepository;
use App\Domain\Model\Contact;


class InMemoryContactRepository implements ContactRepository
{
    /**
     * @var Contact[]
     */
    private array $storage = [];

    private int $nextId = 1;

    public function list(): array
    {
        $storage = $this->storage;
        usort($storage, function (Contact $a, Contact $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });
        return array_values($storage);
    }

    public function create(string $email, string $name): Contact
    {
        $contact = new Contact($this->nextId++, $email, $name, new \DateTimeImmutable());

        $this->storage[$contact->getId()] = $contact;

        return $contact;
    }

    public function delete(int $id): bool
    {
        if (!isset($this->storage[$id])) {
            return false;
        }

        unset($this->storage[$id]);
        return true;
    }

    public function loadByEmail(string $email): ?Contact
    {
        foreach ($this->storage as $contact) {
            if ($contact->getEmailAddress() === $email) {
                return $contact;
            }
        }

        return null;
    }
}