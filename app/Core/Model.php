<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Thin active-record-ish base model over PDO with soft-delete support.
 */
abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDelete = false;

    protected function scope(): string
    {
        return $this->softDelete ? "{$this->table}.deleted_at IS NULL" : '1=1';
    }

    public function find(int $id): ?array
    {
        return Database::first(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND {$this->scope()} LIMIT 1",
            [$id]
        );
    }

    public function findBy(string $column, mixed $value): ?array
    {
        return Database::first(
            "SELECT * FROM {$this->table} WHERE {$column} = ? AND {$this->scope()} LIMIT 1",
            [$value]
        );
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        return Database::all("SELECT * FROM {$this->table} WHERE {$this->scope()} ORDER BY {$orderBy}");
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        return (int) Database::value(
            "SELECT COUNT(*) FROM {$this->table} WHERE {$where} AND {$this->scope()}",
            $params
        );
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        return Database::insert($sql, $data);
    }

    public function update(int $id, array $data): bool
    {
        if (!$data) {
            return false;
        }
        $set = implode(', ', array_map(static fn ($c) => "$c = :$c", array_keys($data)));
        $data['__id'] = $id;
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :__id";
        return Database::run($sql, $data)->rowCount() >= 0;
    }

    public function delete(int $id): bool
    {
        if ($this->softDelete) {
            return Database::run(
                "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = ?",
                [$id]
            )->rowCount() > 0;
        }
        return Database::run(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        )->rowCount() > 0;
    }

    /** Simple offset pagination. */
    public function paginate(int $page, int $perPage, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $total = $this->count($where, $params);
        $rows = Database::all(
            "SELECT * FROM {$this->table} WHERE {$where} AND {$this->scope()} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return [
            'data'      => $rows,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }
}
