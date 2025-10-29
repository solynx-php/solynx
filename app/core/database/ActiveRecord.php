<?php

namespace app\core\database;

use app\core\Model as BaseModel;

abstract class ActiveRecord extends BaseModel implements \JsonSerializable
{
    protected array $attributes = [];
    protected array $original = [];
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected array $hidden = [];
    protected string $primaryKey = 'id';

    abstract public static function table(): string;

    public function __construct(array $data = [])
    {
        parent::__construct();
        if ($data) $this->fill($data);
        $this->original = $this->attributes;
    }

    public function __get($k)
    {
        return $this->attributes[$k] ?? null;
    }
    public function __set($k, $v)
    {
        $this->attributes[$k] = $v;
    }

    public function fill($data): static
    {
        foreach ($data as $k => $v) {
            if ($this->fillable && !in_array($k, $this->fillable, true)) continue;
            if ($this->guarded && in_array($k, $this->guarded, true)) continue;
            $this->attributes[$k] = $this->castIn($k, $v);
        }
        return $this;
    }

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::table(), static::class);
    }

    public static function find($id): ?static
    {
        $pk = (new static)->primaryKey;
        return static::query()->where($pk, '=', $id)->first();
    }

    public static function all()
    {
        $rows = static::query()->get();
        return new Collection($rows);
    }

    public function first(): ?static
    {
        $results = $this->take(1)->get();
        return $results[0] ?? null;
    }

    public function get(): array
    {
        return static::query()->where($this->primaryKey, '=', $this->attributes[$this->primaryKey] ?? 0)->get();
    }

    public function take(int $n): static
    {
        static::query()->take($n);
        return $this;
    }

    public function skip(int $n): static
    {
        static::query()->skip($n);
        return $this;
    }

    public static function create(array $data): static
    {
        $instance = new static();
        $instance->fill($data);
        $instance->save();
        return $instance;
    }

    public static function tableName(): string
    {
        return static::table();
    }

    public function where(string $col, string $op, $val): static
    {
        static::query()->where($col, $op, $val);
        return $this;
    }

    public function orWhere(string $col, string $op, $val): static
    {
        static::query()->orWhere($col, $op, $val);
        return $this;
    }

    public function whereIn(string $col, array $vals): static
    {
        static::query()->whereIn($col, $vals);
        return $this;
    }

    public function orderBy(string $col, string $dir = 'asc'): static
    {
        static::query()->orderBy($col, $dir);
        return $this;
    }

    public function limit(int $n): static
    {
        static::query()->take($n);
        return $this;
    }

    public function offset(int $n): static
    {
        static::query()->skip($n);
        return $this;
    }

    public static function count(): int
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM `" . static::table() . "`");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    public function refresh(): static
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $fresh = static::find($this->attributes[$this->primaryKey]);
            if ($fresh) {
                $this->attributes = $fresh->attributes;
                $this->original = $fresh->original;
            }
        }
        return $this;
    }

    public function reload(): static
    {
        return $this->refresh();
    }

    public function touch(): bool
    {
        $now = date('Y-m-d H:i:s');
        $this->attributes['updated_at'] = $now;
        return $this->save();
    }

    public function fresh(): ?static
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return static::find($this->attributes[$this->primaryKey]);
        }
        return null;
    }

    public function with(string ...$relations): static
    {
        return $this;
    }

    public function save(): bool
    {
        if (method_exists($this, 'rules') && !$this->validate()) return false;

        $pdo = DB::pdo();
        $isUpdate = isset($this->attributes[$this->primaryKey]);
        $data = $this->attributes;

        if ($isUpdate) {
            $cols = array_keys($data);
            $set  = implode(', ', array_map(fn($c) => "`$c`=?", $cols));
            $sql  = "UPDATE `" . static::table() . "` SET $set WHERE `{$this->primaryKey}` = ?";
            $stmt = $pdo->prepare($sql);
            $ok   = $stmt->execute([...array_values($this->castOut($data)), $this->attributes[$this->primaryKey]]);
        } else {
            $cols = array_keys($data);
            $ph   = rtrim(str_repeat('?,', count($cols)), ',');
            $sql  = "INSERT INTO `" . static::table() . "` (`" . implode('`,`', $cols) . "`) VALUES ($ph)";
            $stmt = $pdo->prepare($sql);
            $ok   = $stmt->execute(array_values($this->castOut($data)));
            if ($ok) $this->attributes[$this->primaryKey] = $pdo->lastInsertId();
        }
        $this->original = $this->attributes;
        return $ok;
    }

    public function update(array $attrs): bool
    {
        $this->fill($attrs);
        return $this->save();
    }

    public static function destroy($id): bool
    {
        $instance = static::find($id);
        if ($instance) {
            return $instance->delete();
        }
        return false;
    }

    public function exists(): bool
    {
        return isset($this->attributes[$this->primaryKey]);
    }

    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    public function isClean(): bool
    {
        return !$this->isDirty();
    }

    public function getOriginal($key = null)
    {
        if ($key === null) {
            return $this->original;
        }
        return $this->original[$key] ?? null;
    }

    public function getChanges(): array
    {
        $changes = [];
        foreach ($this->attributes as $k => $v) {
            if (!array_key_exists($k, $this->original) || $this->original[$k] !== $v) {
                $changes[$k] = $v;
            }
        }
        return $changes;
    }



    public function delete(): bool
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM `" . static::table() . "` WHERE `{$this->primaryKey}`=?");
        return $stmt->execute([$this->attributes[$this->primaryKey]]);
    }

    public function toArray(): array
    {
        $a = $this->attributes;
        foreach ($this->hidden as $h) unset($a[$h]);
        return $a;
    }

    // relationships
    protected function hasMany(string $related, string $fk, string $localKey = 'id')
    {
        return $related::query()->where($fk, '=', $this->{$localKey})->get();
    }
    protected function hasOne(string $related, string $fk, string $localKey = 'id')
    {
        return $related::query()->where($fk, '=', $this->{$localKey})->first();
    }
    protected function belongsTo(string $related, string $ownerKey, string $fk)
    {
        return $related::query()->where($ownerKey, '=', $this->{$fk})->first();
    }

    // casting helpers
    private function castIn(string $k, $v)
    {
        return match ($this->casts[$k] ?? null) {
            'int'   => (int)$v,
            'float' => (float)$v,
            'bool'  => (bool)$v,
            'json'  => is_string($v) ? json_decode($v, true) : $v,
            default => $v,
        };
    }
    private function castOut(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = ($this->casts[$k] ?? null) === 'json' ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v;
        }
        return $out;
    }

    public function jsonSerialize(): mixed
    {
        $data = $this->attributes;

        foreach ($this->hidden as $h) unset($data[$h]);

        foreach (get_object_vars($this) as $key => $val) {
            if (in_array($key, ['attributes', 'original', 'fillable', 'guarded', 'casts', 'hidden', 'errors', 'primaryKey'])) {
                continue;
            }
            if ($val instanceof self) {
                $data[$key] = $val->jsonSerialize();
            } elseif (is_array($val)) {
                $data[$key] = array_map(
                    fn($v) => $v instanceof self ? $v->jsonSerialize() : $v,
                    $val
                );
            }
        }

        return $data;
    }
}
