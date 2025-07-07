<?php

namespace App\Boundary\Controller;

use App\Domain\ContactRepository;
use App\Domain\Model\Contact;
use App\Infrastructure\Http\JsonResponse;
use DateTimeInterface;

class ContactController
{
    public function __construct(private readonly ContactRepository $repository)
    {
    }

    public function create()
    {
        if ($data === null) {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (
            !is_array($data) ||
            empty($data['email_address']) ||
            empty($data['name'])
        ) {
            JsonResponse::response(['error' => 'Invalid input'], 400);
            return;
        }

        $email = filter_var($data['email_address'], FILTER_VALIDATE_EMAIL);
        $name = trim($data['name']);

        if (!$email) {
            JsonResponse::response(['error' => 'Invalid email'], 400);
            return;
        }

        if ($name === '') {
            JsonResponse::response(['error' => 'Invalid name'], 400);
            return;
        }

        if ($this->repository->loadByEmail($email)) {
            JsonResponse::response(['error' => 'Duplicate email'], 409);
            return;
        }

        $contact = $this->repository->create($email, $name);

        JsonResponse::response($contact->toDto(), 201);
    }

    public function list(): void
    {
        $contacts = $this->repository->list();
        JsonResponse::response(array_map(fn(Contact $c) => $c->toDto(), $contacts));
    }

    public function delete(string $id): void
    {
        $deleted = $this->repository->delete($id);

        if ($deleted) {
            http_response_code(204);
            JsonResponse::response([], 204);
        } else {
            JsonResponse::response(['error' => 'Contact not found'], 404);
        }
    }
}