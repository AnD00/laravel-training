<?php

namespace Tests\Feature;

use App\Model\Company;
use App\Model\Job;
use Tests\ControllerTestCase;

class JobControllerTest extends ControllerTestCase
{
    /**
     * POST /api/jobs success case
     *
     * @return void
     */
    public function testStoreSuccess()
    {
        $company = factory(Company::class)->create();
        $params = [
            'name' => $this->faker->catchPhrase,
            'category' => $this->faker->word,
            'detail' => $this->faker->text,
            'company_id' => $company->id,
        ];

        $response = $this->post('/api/jobs', $params);
        $response
            ->assertStatus(201)
            ->assertJson($params);
    }

    /**
     * POST /api/jobs error case
     *
     * @return void
     */
    public function testStoreError()
    {
        $company = factory(Company::class)->create();
        $params = [
            'name' => '',
            'category' => $this->faker->word,
            'detail' => $this->faker->text,
            'company_id' => $company->id,
        ];

        $response = $this->post('/api/jobs', $params);
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "name" => [
                            "The name field is required."
                        ],
                    ],
                ]
            );
    }

    /**
     * PATCH /api/jobs/$id success case
     *
     * @return void
     */
    public function testUpdateSuccess()
    {
        $job = factory(Job::class)->create();
        $params = ['category' => 'updated category!'];

        $response = $this->patch('/api/jobs/'.$job->id, $params);
        $response
            ->assertStatus(200)
            ->assertJson($params);
    }

    /**
     * PATCH /api/jobs/$id error case
     *
     * @return void
     */
    public function testUpdateError()
    {
        $job = factory(Job::class)->create();
        $params = ['detail' => ''];

        $response = $this->patch('/api/jobs/'.$job->id, $params);
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "detail" => [
                            "The detail field must have a value."
                        ],
                    ],
                ]
            );
    }

    /**
     * GET /api/jobs/$id success case
     *
     * @return void
     */
    public function testShowSuccess()
    {
        $job = factory(Job::class)->create();

        $response = $this->get('/api/jobs/'.$job->id);
        $response
            ->assertStatus(200)
            ->assertJson($job->toArray());
    }

    /**
     * GET /api/jobs/$id error case
     *
     * @return void
     */
    public function testShowError()
    {
        $id = 0;

        $response = $this->get('/api/jobs/'.$id);
        $response->assertStatus(404);
    }

    /**
     * GET /api/jobs/search success case
     *
     * @return void
     */
    public function testSearchSuccess()
    {
        $job1 = factory(Job::class)->create(
            [
                'name' => 'dummy job name',
                'company_id' => function () {
                    return factory(Company::class)->create(
                        ['name' => 'dummy company name'],
                    )->id;
                },
            ],
        );
        $job2 = factory(Job::class)->create(
            [
                'category' => 'dummy job category',
                'company_id' => function () {
                    return factory(Company::class)->create(
                        ['address' => 'dummy company address'],
                    )->id;
                },
            ],
        );
        $job3 = factory(Job::class)->create(
            [
                'detail' => 'ダミー求人概要',
                'company_id' => function () {
                    return factory(Company::class)->create(
                        ['address' => 'ダミー企業住所'],
                    )->id;
                },
            ],
        );
        $this->refreshElasticsearchIndice();

        // Hit $job1->name
        $params = ['q' => 'name'];
        $response = $this->get('/api/jobs/search?'.http_build_query($params));
        $response
            ->assertStatus(200)
            ->assertJsonFragment($job1->toArray());
        $this->assertEquals(1, count($response->baseResponse->original->toArray()));

        // Hit $job1->company->name & $job2->company->address
        $params = ['q' => 'company'];
        $response = $this->get('/api/jobs/search?'.http_build_query($params));
        $response
            ->assertStatus(200)
            ->assertJsonFragment($job1->toArray())
            ->assertJsonFragment($job2->toArray());
        $this->assertEquals(2, count($response->baseResponse->original->toArray()));

        // Hit $job3->detail
        $params = ['q' => urlencode('求人')];
        $response = $this->get('/api/jobs/search?'.http_build_query($params));
        $response
            ->assertStatus(200)
            ->assertJsonFragment($job3->toArray());
        $this->assertEquals(1, count($response->baseResponse->original->toArray()));

        // Not hit
        $params = ['q' => 'test'];
        $response = $this->get('/api/jobs/search?'.http_build_query($params));
        $response
            ->assertStatus(200)
            ->assertJson([]);
        $this->assertEquals(0, count($response->baseResponse->original->toArray()));
    }

    /**
     * GET /api/jobs/search error case
     *
     * @return void
     */
    public function testSearchError()
    {
        $params = ['q' => '求人'];

        $response = $this->get('/api/jobs/search?'.http_build_query($params));
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "q" => [
                            "The q must be urlencoded."
                        ],
                    ],
                ]
            );
    }
}
