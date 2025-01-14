<?php

namespace Sthom\Kernel\Database;

use PDO;


abstract class AbstractRepository
{
    public readonly SqlBuilder $queryBuilder;
    public readonly PDO $connection;

    public function __construct(
        public readonly string $model
    )
    {
        if (defined($this->model . '::TABLE')) {
            $this->queryBuilder = new SqlBuilder($this->model::TABLE);
            $this->connection = Database::getConnexion();
        } else {
            throw new \Exception("La classe $this->model doit définir une constante TABLE.");
        }
    }

    public final function find(int $id): ?object
    {
        $query = $this->queryBuilder
            ->select()
            ->where('id', '=', $id)
            ->buildSelect();

        return $this->executeSingle($query);
    }

    public final function findAll(): array
    {
        $query = $this->queryBuilder
            ->select()
            ->buildSelect();

        return $this->executeMultiple($query);
    }

    public final function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $builder = $this->queryBuilder->select();

        foreach ($criteria as $field => $value) {
            $builder->where($field, '=', $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $builder->orderBy($field, $direction);
            }
        }

        if ($limit) {
            $builder->limit($limit);
        }

        if ($offset) {
            $builder->offset($offset);
        }

        return $this->executeMultiple($builder->buildSelect());
    }

    public final function findOneBy(array $criteria): ?object
    {
        $result = $this->findBy($criteria, null, 1);
        return !empty($result) ? $result[0] : null;
    }

    public final function save(object $entity): void
    {
        if ($this->hasId($entity)) {
            $this->update($entity);
        } else {
            $this->insert($entity);
        }
    }

    public final function delete(int $id): void
    {
        $query = $this->queryBuilder
            ->where('id', '=', $id)
            ->delete();

        $this->executeStatement($query);
    }

    public final function insert(object $entity): void
    {
        $data = $this->extractData($entity);
        $query = $this->queryBuilder->insert($data);

        $this->executeStatement($query);

        if (method_exists($entity, 'setId')) {
            $entity->setId((int)$this->connection->lastInsertId());
        }
    }

    public final function update(object $entity): void
    {
        $data = $this->extractData($entity);
        $id = $this->getId($entity);

        $query = $this->queryBuilder
            ->where('id', '=', $id)
            ->update($data);

        $this->executeStatement($query);
    }

    public final function executeStatement(QueryResult $query): void
    {
        $stmt = $this->connection->prepare($query->getSQL());
        $stmt->execute($query->getParameters());
    }

    public final function executeSingle(QueryResult $query): ?object
    {
        $stmt = $this->connection->prepare($query->getSQL());
        $stmt->execute($query->getParameters());

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? Hydrator::hydrate($result, $this->model) : null;
    }

    public final function executeMultiple(QueryResult $query): array
    {
        $stmt = $this->connection->prepare($query->getSQL());
        $stmt->execute($query->getParameters());

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn($data) => Hydrator::hydrate($data, $this->model),
            $results
        );
    }

    public final function executeRaw(string $sql, array $parameters = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function extractData(object $entity): array
    {
        // Note: Vous devrez implémenter cette méthode selon votre logique d'extraction
        // des propriétés de l'entité
        return Hydrator::extract($entity);
    }

    private function hasId(object $entity): bool
    {
        return $this->getId($entity) !== null;
    }

    private function getId(object $entity): ?int
    {
        if (method_exists($entity, 'getId')) {
            return $entity->getId();
        }

        if (property_exists($entity, 'id')) {
            $reflection = new \ReflectionProperty($entity, 'id');
            $reflection->setAccessible(true);
            return $reflection->getValue($entity);
        }

        return null;
    }

    public final function customQuery(string $sql, ?array $args = null): mixed
    {
        $query = new QueryResult($sql, $args);
        $stmt = $this->connection->prepare($query->getSQL());
        $stmt->execute($query->getParameters());
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
