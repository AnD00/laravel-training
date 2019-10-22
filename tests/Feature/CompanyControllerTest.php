<?php

namespace Tests\Feature;

use Tests\ControllerTestCase;

class CompanyControllerTest extends ControllerTestCase
{
    /**
     * POST /api/companies success case
     *
     * @return void
     */
    public function testStoreSuccess()
    {
        $params =  [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
        ];

        $response = $this->post('/api/companies', $params);
        $response
            ->assertStatus(201)
            ->assertJson($params);
    }

    /**
     * POST /api/companies error case
     *
     * @return void
     */
    public function testStoreError()
    {
        $params =  [
            'name' => $this->faker->company,
            'address' => '',
        ];

        $response = $this->post('/api/companies', $params);
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" => "The given data was invalid.",
                    "errors" => ["address" => ["The address field is required."]],
                ]
            );
    }
}
