<?php

namespace app\core\form;

class Field
{
    public $model;
    public $attribute;

    public function __construct($model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function renderInput(): string
    {
        $value = htmlspecialchars($this->model->{$this->attribute} ?? '', ENT_QUOTES, 'UTF-8');
        $type = in_array($this->attribute, ['password', 'confirmpassword']) ? 'password' : 'text';
        $errorClass = $this->model->hasError($this->attribute) ? ' is-invalid' : '';
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control%s">', $type, $this->attribute, $value, $errorClass);
    }

    public function __toString(): string
    {
        $error = $this->model->getError($this->attribute);
        $errorClass = $this->model->hasError($this->attribute) ? ' is-invalid' : '';
        $value = htmlspecialchars($this->model->{$this->attribute} ?? '', ENT_QUOTES, 'UTF-8');
        $type = in_array($this->attribute, ['password', 'confirmpassword']) ? 'password' : 'text';

        return sprintf(
            '<div class="mb-3">
                <label class="form-label">%s</label>
                <input type="%s" name="%s" value="%s" class="form-control%s">
                <div class="invalid-feedback">%s</div>
            </div>',
            ucfirst($this->attribute),
            $type,
            $this->attribute,
            $value,
            $errorClass,
            htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8')
        );
    }
}
