<?php

namespace App\Models;

abstract class CategoryModel {
    protected string $category_name;
    protected int $id;
    protected string $__typename;

    public function __construct($data){
        $this->category_name = $data['Category_Name'];
        $this->id = $data['ID'];
        $this->__typename = $data['__typename'];
    }

    abstract public static function findAll(): array;
}