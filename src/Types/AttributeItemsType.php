<?php
namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemsType extends ObjectType {
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