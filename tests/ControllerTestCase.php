<?php

namespace Tests;

use App\Scout\ElasticsearchEngine;
use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * ElasticsearchEngine instance.
     *
     * @var ElasticsearchEngine
     */
    protected $elasticsearchEngine;

    /**
     * ControllerTestCase constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->elasticsearchEngine = new ElasticsearchEngine(
            'scout',
            ClientBuilder::create()
                ->setHosts(['http://localhost:9200'])
                ->build(),
        );
    }

    /**
     * Executed before every test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->elasticsearchEngine->create();
    }

    /**
     * Executed after every test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->elasticsearchEngine->deleteAll();

        parent::tearDown();
    }

    /**
     * Refresh elasticsearch indice.
     *
     * @return void
     */
    protected function refreshElasticsearchIndice(): void
    {
        $this->elasticsearchEngine->refresh();
    }
}
