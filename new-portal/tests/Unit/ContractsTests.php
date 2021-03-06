<?php

namespace Tests\Unit;

use App\Consultants;
use App\Customers;
use Tests\TestCase;

class ContractsTests extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $consultant = Consultants::getByCpf('462.604.768-84');
        $customer = Customers::getByCpf('462.604.768-84');
        $wallets = $customer->wallets();
        $wallet = $wallets->firstOrFail();

        $contract_data = [
            'id_wallet' => $wallet->id_wallet,
            'contract_status' => 'pending',
            'product' => 'Income',
            'value' => '8000.00',
            'started_at' => '2019-07-07',
        ];

        $response = $this->actingAs($consultant, 'consultant')
                         ->post(
                             route('save_new_contract'),
                             $contract_data
                         );
        $response = $this->followRedirects($response);
        $response->assertOk();
    }
}
