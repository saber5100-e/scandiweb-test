<?php
namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Types\ProductsGalleryType;
use App\Types\ProductsAttributesType;
use App\Types\ProductPricesType;

Class AttributeItemsType extends ObjectType {
    public function __construct(){

        parent::__construct([
            "name" => "AttributeItems",
                "fields" => [
                   'Primary_ID' => Type::int(),
                   'ID' => Type::string(),
                   'Display_Value' => Type::string(),
                   'Item_Value' => Type::string(),
                   'Attribute_ID' => Type::int(),
                   '__typename' => Type::string(),
                ]
        ]);
    }
}