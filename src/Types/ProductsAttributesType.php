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
                'Primary_ID' => Type::int(),
                'ID' => Type::string(),
                'Product_ID' => Type::string(),
                'Attribute_Name' => Type::string(),
                'Attribute_Type' => Type::string(),
                '__typename' => Type::string(),
                'Attributes_Items' => Type::listOf($attributeItemsType)
            ]
        ]);
    }
}
