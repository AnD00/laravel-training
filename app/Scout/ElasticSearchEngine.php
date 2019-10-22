<?php

namespace App\Scout;

use Elasticsearch\Client as Elastic;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class ElasticsearchEngine extends Engine
{
    /**
     * Index string variable.
     *
     * @var string
     */
    protected $index;

    /**
     * Elasticsearch client variable.
     *
     * @var Elastic
     */
    protected $elastic;

    /**
     * ElasticsearchEngine constructor.
     *
     * @param string                $index
     * @param \Elasticsearch\Client $elastic
     */
    public function __construct($index, Elastic $elastic)
    {
        $this->index = $index;
        $this->elastic = $elastic;
    }

    /**
     * Elasticsearch indice create.
     *
     * @return callable|array
     */
    public function create()
    {
        $indexCheckResponse = $this->elastic->indices()->get(
            [
                'index' => $this->index,
                'client' => ['ignore' => 404]
            ],
        );
        if (isset($indexCheckResponse['status']) && $indexCheckResponse['status'] == '404') {
            $this->elastic->indices()->create(
                [
                    'index' => $this->index,
                ],
            );
        }
    }

    /**
     * Update the given model in the index.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     *
     * @return void
     */
    public function update($models)
    {
        $params['body'] = [];
        $models->each(
            function ($model) use (&$params) {
                $params['body'][] = [
                    'update' => [
                        '_id'    => $model->getKey(),
                        '_index' => $this->index,
                        '_type'  => $model->searchableAs(),
                    ],
                ];
                $params['body'][] = [
                    'doc'           => $model->toSearchableArray(),
                    'doc_as_upsert' => true,
                ];
            },
        );
        $this->elastic->bulk($params);
    }

    /**
     * Remove the given model from the index.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     *
     * @return void
     */
    public function delete($models)
    {
        $params['body'] = [];

        $models->each(
            function ($model) use (&$params) {
                $params['body'][] = [
                    'delete' => [
                        '_id'    => $model->getKey(),
                        '_index' => $this->index,
                        '_type'  => $model->searchableAs(),
                    ],
                ];
            },
        );
        $this->elastic->bulk($params);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \Laravel\Scout\Builder $builder
     *
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->performSearch(
            $builder,
            array_filter(
                [
                    'filters' => $this->filters($builder),
                    'limit'   => $builder->limit,
                ],
            ),
        );
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \Laravel\Scout\Builder $builder
     * @param int                    $perPage
     * @param int                    $page
     *
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        $result = $this->performSearch(
            $builder,
            [
                'filters' => $this->filters($builder),
                'from'    => (($page * $perPage) - $perPage),
                'limit'   => $perPage,
            ],
        );
        $result['nbPages'] = $result['hits']['total']['value'] / $perPage;

        return $result;
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param \Laravel\Scout\Builder              $builder
     * @param mixed                               $results
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        if ($results['hits']['total']['value'] === 0) {
            return collect();
        }

        $keys = collect($results['hits']['hits'])
            ->pluck('_id')->values()->all();

        $models = $model->withOnSearch()->whereIn(
            $model->getKeyName(),
            $keys
        )->get()->keyBy($model->getKeyName());

        return collect($results['hits']['hits'])->map(
            function ($hit) use ($model, $models) {
                return isset($models[$hit['_id']]) ? $models[$hit['_id']] : null;
            }
        )->filter()->values();
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     *
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     *
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['hits']['total']['value'];
    }

    /**
     * Perform the given search on the engine.
     *
     * @param \Laravel\Scout\Builder $builder
     * @param array                  $options
     *
     * @return void
     */
    protected function performSearch(Builder $builder, $options = [])
    {
        $params = [
            'index' => $this->index,
            'type'  => $builder->index ?: $builder->model->searchableAs(),
            'body'  => [
                'query' => $builder->model->buildSearchQuery($builder),
                '_source' => ['id'],
            ]
        ];

        if ($sort = $this->sort($builder)) {
            $params['body']['sort'] = $sort;
        }

        if (isset($options['filters']) && count($options['filters'])) {
            $params['body']['query']['bool']['filter'] = $options['filters'];
        }

        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $this->elastic,
                $builder->query,
                $params
            );
        }

        return $this->elastic->search($params);
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     *
     * @return void
     */
    public function flush($model)
    {
        //
    }

    /**
     * Refresh indice from the engine.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     *
     * @return void
     */
    public function refresh()
    {
        return $this->elastic->indices()->refresh(
            [
                'index' => $this->index,
            ],
        );
    }

    /**
     * Delete indice from the engine.
     *
     * @return void
     */
    public function deleteAll()
    {
        return $this->elastic->indices()->delete(
            [
                'index' => $this->index,
            ],
        );
    }

    /**
     * Filter on perform search.
     *
     * @param \Laravel\Scout\Builder $builder
     *
     * @return mixed
     */
    public function filters(Builder $builder)
    {
        return collect($builder->wheres)->map(
            function ($value, $key) {
                return [
                    'term' => [
                        $key => $value,
                    ],
                ];
            },
        )->values()->all();
    }

    /**
     * Sort on perform search.
     *
     * @param \Laravel\Scout\Builder $builder
     *
     * @return mixed
     */
    protected function sort(Builder $builder)
    {
        if (count($builder->orders) == 0) {
            return null;
        }

        return collect($builder->orders)->map(
            function ($order) {
                return [$order['column'] => $order['direction']];
            }
        )->toArray();
    }
}
