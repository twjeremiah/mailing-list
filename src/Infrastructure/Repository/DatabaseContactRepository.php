<?php

namespace App\Infrastructure\Repository;

use App\Domain\ContactRepository;
use App\Domain\Model\Contact;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DatabaseContactRepository implements ContactRepository
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @inheritDoc
     * @return array
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function list(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM contact ORDER BY created_at DESC'
        );

        $contacts = [];

        foreach ($rows as $row) {
            $contacts[] = new Contact(
                id: (int) $row['id'],
                emailAddress: $row['email_address'],
                name: $row['name'],
                createdAt: new \DateTimeImmutable($row['created_at']),
            );
        }

        return $contacts;
    }

    /**
     * @throws Exception
     */
    public function create(string $email, string $name): Contact
    {
        $now = new DateTimeImmutable();

        $this->connection->insert('contact', [
            'email_address' => $email,
            'name' => $name,
            'created_at' => $now->format('Y-m-d H:i:s'),
        ]);

        $id = (int) $this->connection->lastInsertId();

        return new Contact($id, $email, $name, $now);
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $this->connection->delete('contact', ['id' => $id]);
        return true;
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function loadByEmail(string $email): ?Contact
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM contact WHERE email_address = :email',
            ['email' => $email]
        );

        if (!$row) {
            return null;
        }

        return new Contact(
            id: (int) $row['id'],
            emailAddress: $row['email_address'],
            name: $row['name'],
            createdAt: new DateTimeImmutable($row['created_at']),
        );
    }
}