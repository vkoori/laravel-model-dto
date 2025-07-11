<?php

namespace Vkoori\EntityDto\Traits;

use Illuminate\Support\Arr;

trait AutoFillableAndCasts
{
    private bool $fillableLoaded = false;
    private bool $castLoaded = false;

    public function getFillable(): array
    {
        if (!$this->fillableLoaded) {
            $fillable = collect($this->getDtoConfig())
                ->filter(fn($field) => Arr::get($field, 'fillable', false))
                ->keys()
                ->toArray();

            $this->mergeFillable($fillable);

            $this->fillableLoaded = true;
        }

        return parent::getFillable();
    }

    public function getCasts(): array
    {
        if (!$this->castLoaded) {
            $casts = collect($this->getDtoConfig())
                ->filter(fn($field) => Arr::get($field, 'cast', false))
                ->mapWithKeys(function ($field, $key) {
                    $type = Arr::get($field, 'type');
                    $type = ltrim($type, '?');

                    if (class_exists($type)) {
                        $type = str_starts_with($type, '\\') ? $type : '\\' . $type;
                        return [$key => $type];
                    }

                    return match ($type) {
                        'int' => [$key => 'integer'],
                        'bool' => [$key => 'boolean'],
                        'float' => [$key => 'float'],
                        'array' => [$key => 'array'],
                        '\Carbon\Carbon' => [$key => 'datetime'],
                        default => [],
                    };
                })
                ->toArray();

            $this->mergeCasts($casts);
        }

        return parent::getCasts();
    }
}
