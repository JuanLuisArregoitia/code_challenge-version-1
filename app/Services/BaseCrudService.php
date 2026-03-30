<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService
{
    abstract protected function model(): string;

    protected array $with = [];

    public function getAll(): Collection
    {
        return $this->model()::with($this->with)->get();
    }

    public function findById(int $id): Model
    {
        return $this->model()::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Model
    {
        $record = $this->model()::create($data);

        return $record->load($this->with);
    }

    public function update(Model $record, array $data): Model
    {
        $record->update($data);

        return $record->load($this->with);
    }

    public function delete(Model $record): void
    {
        $record->delete();
    }
}
