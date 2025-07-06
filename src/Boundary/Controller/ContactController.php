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
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (
            !is_array($data) ||
            empty($data['email_address']) ||
            empty($data['name'])
        ) {
            JsonResponse::response(['error' => 'Invalid input'], 400);
        }

        $email = filter_var($data['email_address'], FILTER_VALIDATE_EMAIL);
        $name = trim($data['name']);

        if (!$email) {
            JsonResponse::response(['error' => 'Invalid email'], 400);
        }

        if ($name === '') {
            JsonResponse::response(['error' => 'Invalid name'], 400);
        }

        if ($this->repository->loadByEmail($email)) {
            JsonResponse::response(['error' => 'Duplicate email'], 409);
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
            exit;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Contact not found']);
            exit;
        }
    }
}