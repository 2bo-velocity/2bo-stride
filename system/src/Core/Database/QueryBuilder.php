<?php

namespace Stride\Core\Database;

use PDO;

class QueryBuilder
{
    private PDO $pdo;
    private string $table;
    private array $wheres = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $orders = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }
    
    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update(array $data): bool
    {
        $set = [];
        $bindings = [];
        
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $bindings[] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
            $bindings = array_merge($bindings, $this->bindings);
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }
}
