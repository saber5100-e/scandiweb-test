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
                'primary_id' => Type::nonNull(Type::int()),
                'id' => Type::nonNull(Type::string()),
                'product_id' => Type::nonNull(Type::string()),
                'attribute_name' => Type::nonNull(Type::string()),
                'attribute_type' => Type::nonNull(Type::string()),
                '__typename' => Type::nonNull(Type::string()),
                'attributes_items' => Type::listOf($attributeItemsType)
            ]
        ]);
    }
}
