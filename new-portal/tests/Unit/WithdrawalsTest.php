<?php

namespace Tests\Unit;

use App\Consultants;
use App\Contracts;
use App\Customers;
use Tests\TestCase;

class WithdrawalsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->makeWithdrawTest();
        $this->sendWithdrawSolicitation();
        $this->sendCancellationSolicitation();
    }

    /**
     * Makes a new Withdraw as a test
     *
     * @return void
     */
    public function makeWithdrawTest() {
        $consultant = Consultants::getByCpf('462.604.768-84');
        $contract = Contracts::firstOrFail();
        $withdraw_data = [
            'value' => 500,
        ];

        $response = $this->actingAs($consultant, 'consultant')
                         ->post(
                            route('make_withdraw', ['contract' => encrypt($contract->id)]),
                            $withdraw_data
                         );

        $other_response = $this->followRedirects($response);
        $other_response->assertOk();
    }

    /**
     * Request a withdraw solicitation as a test
     *
     * @return void
     */
    public function sendWithdrawSolicitation() {
        $customer = Customers::getByCpf('462.604.768-84');
        $contract = Contracts::firstOrFail();
        $solicitation_data = [
            'value' => '1500.0',
            'id_contract' => $contract->id,
            'id_customer' => $customer->id,
        ];

        $response = $this->actingAs($customer)
                         ->post(
                             route('send_withdraw_solicitation', [ 'contract' => encrypt($contract->id) ]),
                             $solicitation_data
                         );
        $response = $this->followRedirects($response);
        $response->assertOk();
    }

    /**
     * Request a cancellation solicitation as a test
     *
     * @return void
     */
    public function sendCancellationSolicitation() {
        $customer = Customers::getByCpf('462.604.768-84');
        $contract = Contracts::firstOrFail();
        $solicitation_data = [
            'id_contract' => $contract->id,
            'id_customer' => $customer->id,
        ];

        $response = $this->actingAs($customer)
                         ->post(
                             route('send_cancellation_solicitation', [ 'contract' => encrypt($contract->id) ]),
                             $solicitation_data
                         );
        $response = $this->followRedirects($response);
        $response->assertOk();
    }
}
