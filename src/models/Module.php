<?php

namespace App\models;

use App\classes\Entity;
use App\utilities\database\Client;

class Module extends Entity
{
    protected ?Client $dataBase;
    protected string $name;
}