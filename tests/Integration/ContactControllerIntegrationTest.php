<?php

namespace Integration;

use App\Boundary\Controller\ContactController;
use App\Domain\ContactRepository;
use App\Infrastructure\Repository\InMemoryContactRepository;
use PHPUnit\Framework\TestCase;

define('TESTING', true);

class ContactControllerIntegrationTest extends TestCase
{
    protected ContactRepository $repository;
    protected ContactController $controller;
    public function setUp(): void
    {
        $this->repository = new InMemoryContactRepository();
        $this->controller = new ContactController($this->repository);
    }

    public function test_create_outputs_correct_json_response(): void
    {
        $data = [
            'email_address' => 'test@test.com',
            'name' => 'test name',
        ];

        ob_start();
        $this->controller->create($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals($data['email_address'], $response['email_address']);
        $this->assertEquals($data['name'], $response['name']);
    }

    public function test_create_with_empty_email_returns_400(): void
    {
        ob_start();
        $this->controller->create(['email_address' => '', 'name' => 'test name']);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertEquals(400, http_response_code());
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid input', $response['error']);
    }

    public function test_create_with_empty_name_returns_400(): void
    {
        ob_start();
        $this->controller->create(['email_address' => 'test@test.com', 'name' => '']);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertEquals(400, http_response_code());
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid input', $response['error']);
    }

    public function test_create_with_invalid_email_returns_400(): void
    {
        $data = [
            'email_address' => 'not-an-email',
            'name' => 'test name',
        ];

        ob_start();
        $this->controller->create($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertEquals(400, http_response_code());
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid email', $response['error']);
    }

    public function test_create_with_duplicate_email_returns_409(): void
    {
        $data = [
            'email_address' => 'test@test.com',
            'name' => 'test name',
        ];

        ob_start();
        $this->controller->create($data);
        ob_end_clean();

        ob_start();
        $this->controller->create($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertEquals(409, http_response_code());
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Duplicate email', $response['error']);
    }

    public function test_list_outputs_all_contacts_sorted(): void
    {
        $this->repository->create('test@test.com', 'test name');
        sleep(1);
        $this->repository->create('test2@test.com', 'test name 2');

        ob_start();
        $this->controller->list();
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $this->assertEquals('test2@test.com', $response[0]['email_address']);
        $this->assertEquals('test@test.com', $response[1]['email_address']);
    }

    public function test_delete_existing_contact_returns_204(): void
    {
        $data = [
            'email_address' => 'testdelete@test.com',
            'name' => 'test name',
        ];

        ob_start();
        $this->controller->create($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->controller->delete((string)$response['id']);
        $this->assertEquals(204, http_response_code());
    }

    public function test_delete_non_existing_contact_returns_404(): void
    {
        ob_start();
        $this->controller->delete('5000');
        $output = ob_get_clean();

        $response = json_decode($output, true);

        $this->assertEquals(404, http_response_code());
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Contact not found', $response['error']);
    }
}