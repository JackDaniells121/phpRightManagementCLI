<?php

namespace App\utilities\database;

use App\classes\Entity;
use App\structures\QueryType;
use PDO;

class Client
{
    private ?PDO $db = null;
    private $dataBaseName;
    private $host;
    private $user;
    private $password;

    public function __construct($dataBaseName, $host, $user, $password)
    {
        $this->dataBaseName = $dataBaseName;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;

        $this->db = new PDO("mysql:host=$host;dbname=$dataBaseName", $user, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function entityExists(Entity $entity): bool
    {
        $className = get_class($entity);
        $id = $entity->getId();
        if ($id === null) {
            return false;
        }
        $query = "SELECT * FROM $className WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false;
    }

//    public function clearDataFromNulls(array &$data): void
//    {
//        foreach ($data as $key => $value)
//        {
//            if ($value == null) {
//                unset($data[$key]);
//            }
//        }
//    }
    public function getQueryFor(int $type, string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $binds = implode(', ', array_map(fn($value) => ":$value", array_keys($data)));

        switch ($type) {
            case QueryType::SELECT:
                $where = implode(' AND ', array_map(fn($value) => "$value = '".$data[$value]."'", array_keys($data)));
                $query = "SELECT * FROM `$table` WHERE $where";
                break;
            case QueryType::INSERT:
                $query = "INSERT INTO `$table` ($columns) VALUES ($binds)";
                break;
            case QueryType::UPDATE:
                $query = "UPDATE `$table` SET ($columns) VALUES ($binds) WHERE id = $data[id]";
                break;
            case QueryType::DELETE:
                $query = "DELETE FROM `$table` WHERE id = $data[id]";
                break;
            default:
                throw new \Exception('Invalid query type');
        }

        return $query;
    }

    public function insertEntity(string $table, array $data): void
    {
        $query = $this->getQueryFor(QueryType::INSERT, $table, $data);
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        $stmt->execute();
    }

    public function updateEntity(string $table, array $data): void
    {
        $query = $this->getQueryFor(QueryType::UPDATE, $table, $data);
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindParam(":$key", $value);
        }

        $stmt->execute();
    }

    public function findAll(string $table, array $data): array
    {
        $query = $this->getQueryFor(QueryType::SELECT, $table, $data);
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];

        foreach ($result as $newData) {
            $className = 'App\\models\\' . ucfirst(rtrim($table, 's'));
            $id = $newData['id'];
            unset($newData['id']);
            $entity = new $className($newData);
            $entity->setId($id);
            $objects[] = $entity;
        }
        return $objects ?? [];
    }

    public function findOne(string $table, array $data): ?Entity
    {
        $query = $this->getQueryFor(QueryType::SELECT, $table, $data);
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        $className = 'App\\models\\' . ucfirst(rtrim($table, 's'));
        $id = $result['id'];
        unset($result['id']);
        $entity = new $className($result);
        $entity->setId($id);
        return $entity;
    }

    public function saveEntity(Entity &$entity)
    {
        $data = ($entity->__toArray());
        $dataFiltered = array_filter($data);    // remove null values
        $table = mb_strtolower((new \ReflectionClass($entity))->getShortName(). 's');
        $id = $entity->getId();

        if ($id == null) {
            $exists = $this->findAll($table, $dataFiltered);
        } else {
            $exists = $this->findAll($table, ['id' => $id]);
        }

        if ($exists) {
            $this->updateEntity($table, $dataFiltered);
        } else {
//            unset($dataFiltered['id']);
            $this->insertEntity($table, $dataFiltered);
            $id = $this->db->lastInsertId();
            $entity->setId($id);
        }
    }

}