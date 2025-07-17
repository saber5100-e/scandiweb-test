<?php

namespace App\Models;

abstract class ProductModel
{
    protected string $id;
    protected bool $inStock;
    protected string $description;
    protected string $productName;
    protected string $category;
    protected string $brand;
    protected array $gallery;
    protected array $attributes;
    protected array $prices;
    protected string $typeName;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->inStock = $data['in_stock'];
        $this->description = $data['description'];
        $this->productName = $data['product_name'];
        $this->category = $data['category'];
        $this->brand = $data['brand'];
        $this->gallery = $data['products_gallery'] ?? [];
        $this->attributes = $data['products_attributes'] ?? [];
        $this->prices = $data['product_prices'] ?? [];
        $this->typeName = $data['__typename'] ?? static::class;
    }
    abstract public function toArray(): array;
}
