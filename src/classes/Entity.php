<?php

namespace App\classes;

use App\utilities\database\Client;

abstract class Entity
{
    private ?int $id = null;
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[strtolower($key).'_id'] ??= $value->getId();
            }
            $this->$key = $value;
        }
    }

    /**
     * @return int
     */
    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function __toArray(): array
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (is_object($value)) {
                $array[$key.'_id'] = $value->getId();
                continue;
            }
            $array[$key] = $value;
        }
        return $array;
    }
}