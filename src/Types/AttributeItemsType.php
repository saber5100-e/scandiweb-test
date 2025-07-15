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
                   'primary_id' => Type::nonNull(Type::int()),
                   'id' => Type::nonNull(Type::string()),
                   'display_value' => Type::nonNull(Type::string()),
                   'item_value' => Type::nonNull(Type::string()),
                   'attribute_id' => Type::nonNull(Type::int()),
                   '__typename' => Type::nonNull(Type::string()),
                ]
        ]);
    }
}
