<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemsType extends ObjectType
{
    public function __construct()
    {

        parent::__construct([
            "name" => "AttributeItems",
                "fields" => [
                   'primary_id' => Type::int(),
                   'id' => Type::string(),
                   'display_value' => Type::string(),
                   'item_value' => Type::string(),
                   'attribute_id' => Type::int(),
                   '__typename' => Type::string(),
                ]
        ]);
    }
}
