<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoriesType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Categories',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'category_name' => Type::nonNull(Type::string()),
                '__typename' => Type::nonNull(Type::string())
            ]
        ]);
    }
}
