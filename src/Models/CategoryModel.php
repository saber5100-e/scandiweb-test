<?php

namespace App\Models;

abstract class CategoryModel
{
    protected string $category_name;
    protected int $id;
    protected string $typeName;

    public function __construct($data)
    {
        $this->category_name = $data['category_name'];
        $this->id = $data['id'];
        $this->typeName = $data['__typename'];
    }

    abstract public static function findAll(): array;
}
