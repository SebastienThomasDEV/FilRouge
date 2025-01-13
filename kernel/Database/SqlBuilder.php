<?php

namespace Sthom\Kernel\Database;
class SqlBuilder
{
    private string $table;
    private array $conditions = [];
    private array $values = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $columns = ['*'];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public final function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    public final function where(string $column, string $operator, mixed $value): self
    {
        $paramName = ':' . str_replace('.', '_', $column);
        $this->conditions[] = "$column $operator $paramName";
        $this->values[$paramName] = $value;
        return $this;
    }

    public final function buildSelect(): QueryResult
    {
        $sql = "SELECT " . implode(', ', $this->columns);
        $sql .= " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return new QueryResult($sql, $this->values);
    }

    public final function insert(array $data): QueryResult
    {
        $columns = array_keys($data); // ['name', 'email']
        $params = array_map(fn($col) => ':' . $col, $columns); // [':name', ':email']
        $sql = "INSERT INTO {$this->table} "; // INSERT INTO users
        $sql .= '(' . implode(', ', $columns) . ') '; // (name, email)
        $sql .= 'VALUES (' . implode(', ', $params) . ')'; // (:name, :email)
        $values = array_combine($params, array_values($data)); // [':name' => 'John', ':email' => 'john@example']
        return new QueryResult($sql, $values); // new QueryResult('INSERT INTO users (name, email) VALUES (:name, :email)', [':name' => 'John', ':email' => 'john@example'])
    }

    public final function update(array $data): QueryResult
    {
        $sets = array_map(function($column) {
            return "$column = :update_$column";
        }, array_keys($data));

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        $values = [];
        foreach ($data as $column => $value) {
            $values[":update_$column"] = $value;
        }

        return new QueryResult($sql, array_merge($values, $this->values));
    }

    public final function delete(): QueryResult
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        return new QueryResult($sql, $this->values);
    }

    public final function join(string $table, string $condition): self
    {
        $this->joins[] = "JOIN $table ON $condition";
        return $this;
    }

    public final function leftJoin(string $table, string $condition): self
    {
        $this->joins[] = "LEFT JOIN $table ON $condition";
        return $this;
    }

    public final function rightJoin(string $table, string $condition): self
    {
        $this->joins[] = "RIGHT JOIN $table ON $condition";
        return $this;
    }

    public final function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    public final function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public final function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public final function count(string $column = '*'): QueryResult
    {
        $sql = "SELECT COUNT($column) as count FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        return new QueryResult($sql, $this->values);
    }

}