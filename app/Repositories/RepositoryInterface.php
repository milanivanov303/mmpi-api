<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function all(array $filters = []);

    public function paginate($perPage = 15, array $filters = []);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id, array $fields);

    public function setFilters(array $filters);
}
