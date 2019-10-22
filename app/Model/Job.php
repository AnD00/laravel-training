<?php

namespace App\Model;

use App\Model\Company;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Laravel\Scout\Searchable;

class Job extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
        'detail',
        'company_id',
    ];

    /**
     * Get the company that owns the job.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Build query when search indexed data.
     *
     * @param Builder $builder
     *
     * @return array
     */
    public function buildSearchQuery(Builder $builder)
    {
        return [
            'bool' => [
                'should' => [
                    ['match' => ['name' => "{$builder->query}"]],
                    ['match' => ['category' => "{$builder->query}"]],
                    ['match' => ['detail' => "{$builder->query}"]],
                    ['match' => ['company.name' => "{$builder->query}"]],
                    ['match' => ['company.address' => "{$builder->query}"]],
                ],
            ],
        ];
    }

    /**
     * Load relationship data when search indexed data.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function withOnSearch()
    {
        return $this->with('company');
    }

    /**
     * Get the indexable data array for the Job model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $job_array = $this->toArray();
        $company_array = ['company' => $this->company->toArray()];
        return array_merge($job_array, $company_array);
    }
}
