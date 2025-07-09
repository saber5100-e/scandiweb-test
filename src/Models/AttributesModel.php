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
        $this->primary_id = $data['Primary_ID'];
        $this->id = $data['ID'];
        $this->product_id = $data['Product_ID'];
        $this->attribute_name = $data['Attribute_Name'];
        $this->attribute_type = $data['Attribute_Type'];
        $this->typeName = $data['__typename'];
        $this->attributes_items = $data['Attributes_Items'];
    }

    abstract public static function getAttributes(string $productId, mysqli $conn): array;

    public function toArray(): array
    {
        return [
            'Primary_ID' => $this->primary_id,
            'ID' => $this->id,
            'Product_ID' => $this->product_id,
            'Attribute_Name' => $this->attribute_name,
            'Attribute_Type' => $this->attribute_type,
            '__typename' => $this->typeName,
            'Attributes_Items' => $this->attributes_items
        ];
    }
}
