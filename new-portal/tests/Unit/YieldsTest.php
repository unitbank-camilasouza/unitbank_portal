<?php

namespace Tests\Feature;

use App\Consultants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class YieldsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $yield_data = [
            'id_contract' => 6,
            'value' => 1000
        ];
        $consultant = Consultants::getByCpf('462.604.768-84');
        $response = $this->actingAs($consultant, 'consultant')
                         ->post(
                             route('approves_yield'),
                             $yield_data
                         );

        dd($response);
        $response->assertStatus(200);
    }
}
