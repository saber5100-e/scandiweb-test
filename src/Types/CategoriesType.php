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
                'id' => Type::int(),
                'category_name' => Type::string(),
                '__typename' => Type::string()
            ]
        ]);
    }
}
