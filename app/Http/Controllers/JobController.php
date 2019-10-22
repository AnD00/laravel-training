<?php

namespace App\Http\Controllers;

use App\Http\Requests\Job\SearchRequest;
use App\Http\Requests\Job\StoreRequest;
use App\Http\Requests\Job\UpdateRequest;
use App\Repositories\Job\RepositoryInterface;

class JobController extends Controller
{
    private $job;

    public function __construct(RepositoryInterface $job)
    {
        $this->job = $job;
    }

    /**
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        return $this->job->store($request->all());
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateRequest $request, int $id)
    {
        return $this->job->update($id, $request->all());
    }

    /**
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->job->findById($id);
    }

    /**
     * @param SearchRequest $request
     * @return Response
     */
    public function search(SearchRequest $request)
    {
        return $this->job->searchByWord($request->q);
    }
}
