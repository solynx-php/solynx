<?php

namespace app\core\database;

class Blueprint
{
    protected string $table;
    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[] = "{$name} BIGINT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = [
            'definition' => "{$name} VARCHAR({$length})",
            'name' => $name
        ];
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function timestamp(string $name, string $default = 'CURRENT_TIMESTAMP'): self
    {
        $this->columns[] = "{$name} TIMESTAMP DEFAULT {$default}";
        return $this;
    }

    public function unique(): self
    {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $col = &$this->columns[$lastIndex];
            // append UNIQUE only once
            if (!str_contains($col['definition'], 'UNIQUE')) {
                $col['definition'] .= ' UNIQUE';
            }
        }
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "{$name} INT";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "{$name} BOOLEAN";
        return $this;
    }

    public function float(string $name): self
    {
        $this->columns[] = "{$name} FLOAT";
        return $this;
    }

    public function date(string $name): self
    {
        $this->columns[] = "{$name} DATE";
        return $this;
    }

    public function datetime(string $name): self
    {
        $this->columns[] = "{$name} DATETIME";
        return $this;
    }

    public function dropColumn(string $name): self
    {
        return $this;
    }

    public function renameColumn(string $from, string $to): self
    {
        return $this;
    }

    public function modifyColumn(string $name, string $newDefinition): self
    {
        return $this;
    }

    public function addForeignKey(string $column, string $referencedTable, string $referencedColumn): self
    {
        $this->columns[] = "FOREIGN KEY ({$column}) REFERENCES {$referencedTable}({$referencedColumn})";
        return $this;
    }

    public function dropForeignKey(string $column): self
    {
        return $this;
    }

    public function addIndex(string $column): self
    {
        $this->columns[] = "INDEX ({$column})";
        return $this;
    }

    public function dropIndex(string $column): self
    {
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function softDeletes(): self
    {
        $this->columns[] = "deleted_at TIMESTAMP NULL";
        return $this;
    }

    public function engine(string $engine): self
    {
        return $this;
    }

    public function charset(string $charset): self
    {
        return $this;
    }

    public function collate(string $collation): self
    {
        return $this;
    }

    public function primary(string $column): self
    {
        $this->columns[] = "PRIMARY KEY ({$column})";
        return $this;
    }

    public function uniqueIndex(string $column): self
    {
        $this->columns[] = "UNIQUE INDEX ({$column})";
        return $this;
    }

    public function indexColumns(array $columns): self
    {
        $cols = implode(", ", $columns);
        $this->columns[] = "INDEX ({$cols})";
        return $this;
    }

    public function dropTableIfExists(): self
    {
        return $this;
    }

    public function renameTable(string $newName): self
    {
        return $this;
    }

    public function modifyTable(callable $callback): self
    {
        $callback($this);
        return $this;
    }

    public function addTimestamps(): self
    {
        return $this->timestamps();
    }

    public function addSoftDeletes(): self
    {
        return $this->softDeletes();
    }

    public function dropTimestamps(): self
    {
        return $this;
    }

    public function dropSoftDeletes(): self
    {
        return $this;
    }

    public function addColumn(string $definition): self
    {
        $this->columns[] = $definition;
        return $this;
    }

    public function removeColumn(string $name): self
    {
        return $this;
    }

    public function renameTableTo(string $newName): self
    {
        return $this;
    }

    public function changeColumn(string $name, string $newDefinition): self
    {
        return $this;
    }

    public function addForeign(string $column, string $referencedTable, string $referencedColumn): self
    {
        return $this->addForeignKey($column, $referencedTable, $referencedColumn);
    }

    public function removeForeign(string $column): self
    {
        return $this;
    }

    public function addIdx(string $column): self
    {
        return $this->addIndex($column);
    }

    public function removeIdx(string $column): self
    {
        return $this;
    }

    public function addUniqueIdx(string $column): self
    {
        return $this->uniqueIndex($column);
    }

    public function removeUniqueIdx(string $column): self
    {
        return $this;
    }

    public function addPrimaryKey(string $column): self
    {
        return $this->primary($column);
    }

    public function removePrimaryKey(string $column): self
    {
        return $this;
    }

    public function addUniqueKey(string $column): self
    {
        return $this->uniqueIndex($column);
    }

    public function removeUniqueKey(string $column): self
    {
        return $this;
    }

    public function addIdxColumns(array $columns): self
    {
        return $this->indexColumns($columns);
    }

    public function removeIdxColumns(array $columns): self
    {
        return $this;
    }

    public function nullable(string $name = ''): self
    {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $this->columns[$lastIndex] .= " NULL";
        }
        return $this;
    }


    public function toSql(): string
    {
        $parts = [];
        foreach ($this->columns as $col) {
            if (is_array($col)) {
                $parts[] = $col['definition'];
            } else {
                $parts[] = $col;
            }
        }
        $cols = implode(",\n    ", $parts);
        return "CREATE TABLE {$this->table} (\n    {$cols}\n);";
    }
}
