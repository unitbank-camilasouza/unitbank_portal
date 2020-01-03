<?php

namespace Tests\Unit;

use App\Consultants;
use App\Contracts;
use App\CurrentContracts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $consultant = Consultants::getByCpf('462.604.768-84');
        $contract = Contracts::findOrFail(6);
        $withdraw_data = [
            'value' => 3000,
        ];

        $response = $this->actingAs($consultant, 'consultant')
                         ->post(
                            route('make_withdraw', ['contract' => encrypt($contract->id)]),
                            $withdraw_data
                         );

        dd($response);
    }
}
