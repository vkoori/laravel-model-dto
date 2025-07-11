<?php

namespace Vkoori\EntityDto;

use Illuminate\Support\Str;

abstract class BaseDTO
{
    private array $setColumns = [];

    protected function markSet(string $column): void
    {
        $this->setColumns[] = $column;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->setColumns as $column) {
            $method = 'get' . ucfirst(Str::camel($column));
            if (method_exists($this, $method)) {
                $data[$column] = $this->{$method}();
            }
        }

        return $data;
    }

    public static function fromModel($model): static
    {
        $dto = new static();

        foreach ($model->getAttributes() as $key => $value) {
            $setter = 'set' . ucfirst(Str::camel($key));
            if (method_exists($dto, $setter)) {
                $dto->{$setter}($model->{$key});
            }
        }

        return $dto;
    }
}
