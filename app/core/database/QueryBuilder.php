<?php
namespace app\core\database;

use PDO;

class QueryBuilder
{
    private PDO $pdo;
    private string $table;
    private string $model;

    private array $select = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private ?string $order = null;
    private ?int $limit = null;
    private ?int $offset = null;

    private array $relations = [];

    public function __construct(string $table, string $model){
        $this->pdo = DB::pdo();
        $this->table = $table;
        $this->model = $model;
    }

    public function select(string ...$cols): self {
        if ($cols) $this->select = $cols;
        return $this;
    }

    public function where(string $col, string $op, $val, string $bool='AND'): self {
        $this->wheres[] = [$bool, "`$col` $op ?"];
        $this->bindings[] = $val;
        return $this;
    }

    public function orWhere(string $col, string $op, $val): self {
        return $this->where($col, $op, $val, 'OR');
    }

    public function whereIn(string $col, array $vals): self {
        $ph = rtrim(str_repeat('?,', count($vals)), ',');
        $this->wheres[] = ['AND', "`$col` IN ($ph)"];
        array_push($this->bindings, ...$vals);
        return $this;
    }

    public function orderBy(string $col, string $dir='asc'): self {
        $this->order = "`$col` " . strtoupper($dir);
        return $this;
    }

    public function take(int $n): self {
        $this->limit = $n;
        return $this;
    }

    public function skip(int $n): self {
        $this->offset = $n;
        return $this;
    }

    public function with(string ...$relations): self
    {
        $this->relations = $relations;
        return $this;
    }

    private function sql(): string
    {
        $select = 'SELECT ' . implode(',', $this->select) . ' FROM `' . $this->table . '`';
        $where = '';
        if ($this->wheres) {
            $first = true;
            $buf = '';
            foreach ($this->wheres as [$bool, $frag]) {
                $buf .= $first ? $frag : " $bool $frag";
                $first = false;
            }
            $where = ' WHERE ' . $buf;
        }
        $order  = $this->order  ? " ORDER BY {$this->order}" : '';
        $limit  = $this->limit  ? " LIMIT {$this->limit}" : '';
        $offset = $this->offset ? " OFFSET {$this->offset}" : '';
        return $select . $where . $order . $limit . $offset;
    }

    public function get(): array
    {
        $stmt = $this->pdo->prepare($this->sql());
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $m = $this->model;
        $models = array_map(fn($r) => new $m($r), $rows);

        foreach ($this->relations as $rel) {
            foreach ($models as $model) {
                if (method_exists($model, $rel)) {
                    $related = $model->$rel();
                    $model->$rel = $related;
                }
            }
        }

        return $models;
    }

    public function first(): ?object
    {
        $this->take(1);
        $stmt = $this->pdo->prepare($this->sql());
        $stmt->execute($this->bindings);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        $m = $this->model;
        $model = new $m($row);

        foreach ($this->relations as $rel) {
            if (method_exists($model, $rel)) {
                $model->$rel = $model->$rel();
            }
        }

        return $model;
    }

    public function create(array $data): object {
        $m = new ($this->model)($data);
        $m->save();
        return $m;
    }

    public function count(): int
    {
        $orig = $this->select;
        $this->select = ['COUNT(*) AS c'];
        $stmt = $this->pdo->prepare($this->sql());
        $stmt->execute($this->bindings);
        $this->select = $orig;
        return (int)$stmt->fetchColumn();
    }
}
