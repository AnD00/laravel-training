<?php

namespace App\Repositories\Company;

use App\Model\Company;

class Repository implements RepositoryInterface
{
    /**
     * The Company repository implementation
     *
     * @var Company
     */
    protected $company;

    /**
     * Instantiate a new Company instance
     *
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Store Company data
     *
     * @param array $data
     *
     * @return Company
     */
    public function store(array $data)
    {
        return $this->company->create($data);
    }
}
