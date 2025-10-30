<?php

namespace app\core\database;

class Collection extends \ArrayObject implements \JsonSerializable
{
    public function with(string ...$relations): static
    {
        foreach ($this->getArrayCopy() as $model) {
            foreach ($relations as $relation) {
                if (method_exists($model, $relation)) {
                    $related = $model->$relation();
                    if ($related instanceof ActiveRecord) {
                        $model->$relation = $related;
                    } elseif (is_array($related)) {
                        $model->$relation = array_map(
                            fn($r) => method_exists($r, 'toArray') ? $r->toArray() : $r,
                            $related
                        );
                    }
                }
            }
        }
        return $this;
    }

    public function toArray()
    {
        return array_map(
            fn($m) => method_exists($m, 'toArray') ? $m->toArray() : $m,
            $this->getArrayCopy()
        );
    }
    public function jsonSerialize()
    {
        return array_map(
            fn($m) => $m instanceof ActiveRecord ? $m->jsonSerialize() : $m,
            $this->getArrayCopy()
        );
    }
}
