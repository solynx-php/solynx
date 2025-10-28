<?php

namespace app\core\database;

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $foreignKeys = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    // ------------------------------------
    // BASIC COLUMNS
    // ------------------------------------
    public function id(string $name = 'id'): self
    {
        $this->columns[] = "`{$name}` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "`{$name}` INT";
        return $this;
    }

    public function bigInteger(string $name): self
    {
        $this->columns[] = "`{$name}` BIGINT";
        return $this;
    }

    public function unsignedBigInteger(string $name): self
    {
        $this->columns[] = "`{$name}` BIGINT UNSIGNED";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "`{$name}` VARCHAR({$length})";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "`{$name}` TEXT";
        return $this;
    }

    public function float(string $name): self
    {
        $this->columns[] = "`{$name}` FLOAT";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "`{$name}` BOOLEAN";
        return $this;
    }

    public function date(string $name): self
    {
        $this->columns[] = "`{$name}` DATE";
        return $this;
    }

    public function datetime(string $name): self
    {
        $this->columns[] = "`{$name}` DATETIME";
        return $this;
    }

    public function timestamp(string $name, string $default = 'CURRENT_TIMESTAMP'): self
    {
        $this->columns[] = "`{$name}` TIMESTAMP DEFAULT {$default}";
        return $this;
    }

    public function nullable(): self
    {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $this->columns[$lastIndex] .= " NULL";
        }
        return $this;
    }

    // ------------------------------------
    // MODIFIERS
    // ------------------------------------
    public function unique(): self
    {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $this->columns[$lastIndex] .= " UNIQUE";
        }
        return $this;
    }

    public function default($value): self
    {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $val = is_string($value) ? "'{$value}'" : $value;
            $this->columns[$lastIndex] .= " DEFAULT {$val}";
        }
        return $this;
    }

    // ------------------------------------
    // TIMESTAMPS / SOFT DELETES
    // ------------------------------------
    public function timestamps(): self
    {
        $this->columns[] = "`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function softDeletes(): self
    {
        $this->columns[] = "`deleted_at` DATETIME NULL";
        return $this;
    }

    // ------------------------------------
    // INDEXES
    // ------------------------------------
    public function primary(string $column): self
    {
        $this->columns[] = "PRIMARY KEY (`{$column}`)";
        return $this;
    }

    public function uniqueIndex(string $column): self
    {
        $this->columns[] = "UNIQUE INDEX (`{$column}`)";
        return $this;
    }

    public function index(string $column): self
    {
        $this->columns[] = "INDEX (`{$column}`)";
        return $this;
    }

    // ------------------------------------
    // FOREIGN KEYS (real implementation)
    // ------------------------------------
    public function foreign(string $column): self
    {
        $this->foreignKeys[] = [
            'column' => $column,
            'references' => null,
            'on' => null,
            'onDelete' => null,
            'onUpdate' => null,
        ];
        return $this;
    }

    public function references(string $column): self
    {
        $index = count($this->foreignKeys) - 1;
        if ($index >= 0) {
            $this->foreignKeys[$index]['references'] = $column;
        }
        return $this;
    }

    public function on(string $table): self
    {
        $index = count($this->foreignKeys) - 1;
        if ($index >= 0) {
            $this->foreignKeys[$index]['on'] = $table;
        }
        return $this;
    }

    public function onDelete(string $action): self
    {
        $index = count($this->foreignKeys) - 1;
        if ($index >= 0) {
            $this->foreignKeys[$index]['onDelete'] = strtoupper($action);
        }
        return $this;
    }

    public function onUpdate(string $action): self
    {
        $index = count($this->foreignKeys) - 1;
        if ($index >= 0) {
            $this->foreignKeys[$index]['onUpdate'] = strtoupper($action);
        }
        return $this;
    }

    // ------------------------------------
    // COMPILE SQL
    // ------------------------------------
    public function toSql(): string
    {
        $parts = $this->columns;

        foreach ($this->foreignKeys as $fk) {
            if ($fk['column'] && $fk['references'] && $fk['on']) {
                $line = "FOREIGN KEY (`{$fk['column']}`) REFERENCES `{$fk['on']}`(`{$fk['references']}`)";
                if ($fk['onDelete']) {
                    $line .= " ON DELETE {$fk['onDelete']}";
                }
                if ($fk['onUpdate']) {
                    $line .= " ON UPDATE {$fk['onUpdate']}";
                }
                $parts[] = $line;
            }
        }

        $sql = "CREATE TABLE `{$this->table}` (\n    " . implode(",\n    ", $parts) . "\n)";
        $sql .= " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        return $sql;
    }
}
