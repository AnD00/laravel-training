<?php

namespace App\Repositories\Job;

use App\Model\Job;

class Repository implements RepositoryInterface
{
    /**
     * The Job repository implementation
     *
     * @var Job
     */
    protected $job;

    /**
     * Instantiate a new Job instance
     *
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Store Job data
     *
     * @param array $data
     *
     * @return mixed
     */
    public function store(array $data)
    {
        return $this->job->with('company')->create($data);
    }

    /**
     * Update Job data
     *
     * @param int   $id
     * @param array $data
     *
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $record = $this->job->with('company')->findOrFail($id);
        $record->fill($data)->save();
        return $record;
    }

    /**
     * Find Job data by id
     *
     * @param int $id
     *
     * @return mixed
     */
    public function findById(int $id)
    {
        return $this->job->with('company')->findOrFail($id);
    }

    /**
     * Search Job data by keyword
     *
     * @param string $keyword
     *
     * @return mixed
     */
    public function searchByWord(string $keyword)
    {
        $query = urldecode($keyword);
        return $this->job->search($query)->get();
    }
}
