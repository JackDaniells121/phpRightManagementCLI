<?php

namespace App\models;

use App\classes\Entity;

class Right extends Entity
{
    protected ?User $user = null;
    protected ?Group $group = null;
    protected ?Module $module = null;
    protected ?ModuleFunction $moduleFunction = null;

}