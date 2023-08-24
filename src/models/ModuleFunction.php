<?php

namespace App\models;

use App\classes\Entity;

class ModuleFunction extends Entity
{
    protected string $name;
    protected ?Module $module;

    public function getName(): string
    {
        return $this->name;
    }
}