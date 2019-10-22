<?php

namespace App\Repositories\Company;

interface RepositoryInterface
{
    /**
     * Store function interface
     *
     * @param array $data
     *
     * @return RepositoryInterface
     */
    public function store(array $data);
}
