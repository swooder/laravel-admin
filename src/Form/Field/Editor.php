<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected static $js = [
        '/ckeditor.js',
    ];

    public function render()
    {
        $this->script = "CKEDITOR.replace('{$this->column}');";

        return parent::render();
    }
}
