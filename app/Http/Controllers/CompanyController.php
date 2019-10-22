<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreRequest;
use App\Repositories\Company\RepositoryInterface;

class CompanyController extends Controller
{
    private $company;

    public function __construct(RepositoryInterface $company)
    {
        $this->company = $company;
    }

    /**
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        return $this->company->store($request->all());
    }
}
