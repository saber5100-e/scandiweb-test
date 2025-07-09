<?php

namespace App\Models;

use mysqli;

abstract class AttributesModel
{
    protected int $primary_id;
    protected string $id;
    protected string $product_id;
    protected string $attribute_name;
    protected string $attribute_type;
    protected string $typeName;
    protected array $attributes_items;

    public function __construct($data)
    {
        $this->primary_id = $data['primary_id'];
        $this->id = $data['id'];
        $this->product_id = $data['product_id'];
        $this->attribute_name = $data['attribute_name'];
        $this->attribute_type = $data['attribute_type'];
        $this->typeName = $data['__typename'];
        $this->attributes_items = $data['attributes_items'];
    }

    abstract public static function getAttributes(string $productId, mysqli $conn): array;

    public function toArray(): array
    {
        return [
            'primary_id' => $this->primary_id,
            'id' => $this->id,
            'product_id' => $this->product_id,
            'attribute_name' => $this->attribute_name,
            'attribute_type' => $this->attribute_type,
            '__typename' => $this->typeName,
            'attributes_items' => $this->attributes_items
        ];
    }
}
