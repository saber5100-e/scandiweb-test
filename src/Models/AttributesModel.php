<?php

namespace App\Models;

use mysqli;

abstract class AttributesModel
{
    protected int $primaryId;
    protected string $id;
    protected string $productId;
    protected string $attributeName;
    protected string $attributeType;
    protected string $typeName;
    protected array $attributesItems;

    public function __construct($data)
    {
        $this->primaryId = $data['primary_id'];
        $this->id = $data['id'];
        $this->productId = $data['product_id'];
        $this->attributeName = $data['attribute_name'];
        $this->attributeType = $data['attribute_type'];
        $this->typeName = $data['__typename'];
        $this->attributesItems = $data['attributes_items'];
    }

    abstract public static function getAttributes(string $productId, mysqli $conn): array;

    public function toArray(): array
    {
        return [
            'primary_id' => $this->primaryId,
            'id' => $this->id,
            'product_id' => $this->productId,
            'attribute_name' => $this->attributeName,
            'attribute_type' => $this->attributeType,
            '__typename' => $this->typeName,
            'attributes_items' => $this->attributesItems
        ];
    }
}
