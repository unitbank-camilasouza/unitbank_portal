<?php

namespace Tests\Unit;

use App\Admins;
use App\Consultants;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RegisterTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * Tests the Consultant's register
     *
     * @return void
     */
    public function testBasicTest() {
        $this->withoutMiddleware();

        $response = $this->registerConsultant();
        $response = $this->followRedirects($response);
        $response->assertOk();
        $response = $this->registerCustomer();
        $response = $this->followRedirects($response);
        $response->assertOk();
    }

    /**
     * Register a new test Consultant
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function registerConsultant() {
        $admin = Admins::where('login', 'admin')->firstOrFail();

        $register_data = [
            'cpf' => '462.604.768-84',
            'first_name' => 'Davi',
            'last_name' => 'Mendes',
            'password' => '$2y$10$gMqDUl3iNKyM0vPDABR3L.xMo6D1mXVwDGcODZAhZ4hMV1uBb5oZu',
            'street' => 'Tv. Vitória Silva Santos',
            'number' => '20',
            'complement' => 'Casa 2',
            'region' => 'Parque Paineiras',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'Brasil',
            'email' => 'davi.mendes.dev@gmail.com',
            'phone_number' => '(11) 99927-6841',
        ];

        $response = $this->actingAs($admin, 'admin')
                           ->post(
                               route('register_consultant'),
                               $register_data
                            );
        return $response;
    }

    /**
     * Register a new test Customer
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function registerCustomer() {
        $consultant = Consultants::where('cpf', '462.604.768-84')->firstOrFail();
        $register_data = [
            'cpf' => '518.072.700-68',
            'first_name' => 'Davi',
            'last_name' => 'Mendes',
            'password' => '$2y$10$gMqDUl3iNKyM0vPDABR3L.xMo6D1mXVwDGcODZAhZ4hMV1uBb5oZu',
            'street' => 'Tv. Vitória Silva Santos',
            'number' => '20',
            'complement' => 'Casa 2',
            'region' => 'Parque Paineiras',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'Brasil',
            'email' => 'davi.mendes2010@gmail.com',
            'phone_number' => '(11) 99927-6841',
            'person_type' => 'physical_person',
            'nationality' => 'Brazilian',
            'born_date' => '2001-06-06',
            'gender' => 'male',
            'father_name' => 'Antonio Carlos Mariano Pimentel',
            'mother_name' => 'Gisele Mendes dos Santos',
            'marital_status' => 'single',
            'document_type' => 'cpf',
            'doc_number' => '462.604.768-84',
            'issue_date' => '2012-07-21',
            'issuing_organ' => 'SST',
            'uf' => 'SP',
        ];

        $response = $this->actingAs($consultant, 'consultant')
                           ->post(
                               route('register_customer'),
                               $register_data
                            );
        return $response;
    }
}
