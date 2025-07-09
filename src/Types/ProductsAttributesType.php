<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Types\AttributeItemsType;

class ProductsAttributesType extends ObjectType
{
    public function __construct()
    {
        $attributeItemsType = new AttributeItemsType();

        parent::__construct([
            'name' => 'ProductAttributes',
            'fields' => [
                'primary_id' => Type::int(),
                'id' => Type::string(),
                'product_id' => Type::string(),
                'attribute_name' => Type::string(),
                'attribute_type' => Type::string(),
                '__typename' => Type::string(),
                'attributes_items' => Type::listOf($attributeItemsType)
            ]
        ]);
    }
}
