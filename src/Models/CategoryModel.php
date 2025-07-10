<?php

namespace App\Models;

abstract class CategoryModel
{
    protected string $categoryName;
    protected int $id;
    protected string $typeName;

    public function __construct($data)
    {
        $this->categoryName = $data['category_name'];
        $this->id = $data['id'];
        $this->typeName = $data['__typename'];
    }

    abstract public static function findAll(): array;
}
