<?php

namespace App\models;

use App\classes\Entity;

class User extends Entity
{
    protected string $username;
    protected ?Group $group = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->username;
    }

    /**
     * @param string username
     */
    public function setName(string $username): void
    {
        $this->username = trim($username);
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getGroupId(): ?int
    {
        if ($this->group === null) {
            $userArray = $this->__toArray();
            if ($userArray['group_id'] !== null) {
                return $userArray['group_id'];
            }
        } else {
            return $this->group->getId();
        }
        return null;
    }

}