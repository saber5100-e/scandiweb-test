<?php

namespace App\Models;

abstract class ProductModel {
    protected string $id;
    protected bool $in_stock;
    protected string $description;
    protected string $productName;
    protected string $category;
    protected string $brand;
    protected array $gallery;
    protected array $attributes;
    protected array $prices;
    protected string $__typename;

    public function __construct(array $data) {
        $this->id = $data['ID'];
        $this->in_stock = $data['In_Stock'];
        $this->description = $data['Description'];
        $this->productName = $data['Product_Name'];
        $this->category = $data['Category'];
        $this->brand = $data['Brand'];
        $this->gallery = $data['Products_gallery'] ?? [];
        $this->attributes = $data['Products_Attributes'] ?? [];
        $this->prices = $data['Product_Prices'] ?? [];
        $this->__typename = $data['__typename'] ?? static::class;
    }

    abstract public function toArray(): array;
}