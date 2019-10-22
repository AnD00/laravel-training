<?php

namespace App\Repositories\Job;

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

    /**
     * Update function interface
     *
     * @param int   $id
     * @param array $data
     *
     * @return RepositoryInterface
     */
    public function update(int $id, array $data);

    /**
     * Find by id function interface
     *
     * @param int $id
     *
     * @return RepositoryInterface
     */
    public function findById(int $id);

    /**
     * Search by keyword function interface
     *
     * @param string $keyword
     *
     * @return RepositoryInterface
     */
    public function searchByWord(string $keyword);
}
