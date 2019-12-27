<?php

namespace Tests\Unit;

use App\Consultants;
use App\Customers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $consultant = Consultants::findOrFail('cpf', '462.604.768-84');
        $customer = Customers::findOrFail('cpf', '462.604.768-84');
        $wallet = $customer->wallets();
        $contract_data = [
            'id_wallet' => $wallet->id,
            'contract_status' => '',    # a decidir
            'product' => '',            # a decidir
            'value' => '5000.00',
            'started_at' => '2019-07-07',
        ];
        $response = $this->actingAs($consultant, 'consultant')
                         ->post(
                             route('save_new_contract'),
                            $contract_data
                        );

        dd($response);
    }
}
