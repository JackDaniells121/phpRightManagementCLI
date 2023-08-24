<?php

namespace App\classes;

use App\models\Group;
use App\models\User;
use App\utilities\database\Client;

class RightManagement
{
    private Client $dataBase;

    public function __construct(Client $dataBaseClient)
    {
        $this->dataBase = $dataBaseClient;
    }

    public function getFunctionListFromRights(array $rights)
    {
        $functionsAccessList = [];
        $modulesAccessList = [];

        foreach ($rights as $right) {
            if ($right->module_id !== null) {
                $module = $this->dataBase->findOne('modules', ['id' => $right->module_id]);
                $modulesAccessList[] = $module;
            }
            if ($right->modulefunction_id !== null) {
                $function = $this->dataBase->findOne('modulefunctions', ['id' => $right->modulefunction_id]);
                $functionsAccessList[] = $function;
            }
        }

        foreach ($modulesAccessList as $module) {
            $functions = $this->dataBase->findAll('modulefunctions', ['module_id' => $module->getId()]);
            foreach ($functions as $function) {
                $functionsAccessList[] = $function;
            }
        }
        return array_unique($functionsAccessList, SORT_REGULAR);
    }

    public function getFunctionsForGroup(Group $group)
    {
        $rights = $this->dataBase->findAll('rights', ['group_id' => $group->getId()]);

        return $this->getFunctionListFromRights($rights);
    }

    function getFunctionsForUser(User $user)
    {
        $rights = $this->dataBase->findAll('rights', ['user_id' => $user->getId()]);

        return $this->getFunctionListFromRights($rights);
    }

    public function getRightsForUser(User $user)
    {
        $group = $this->dataBase->findOne('groups', ['id' => $user->getGroupId()]);

        $functions = $this->getFunctionsForGroup($group);
        $functionsForUser = $this->getFunctionsForUser($user);

        return array_unique(array_merge($functions, $functionsForUser), SORT_REGULAR);
    }

    public function checkRight(User $user, string $functionName): bool
    {
        $functionsArray = $this->getRightsForUser($user);
        $found = false;

        array_map(function ($right) use (&$found, $functionName) {
            if ($right->getName() === $functionName) {
                $found = true;
            }
        }, $functionsArray);

        return $found;
    }
}