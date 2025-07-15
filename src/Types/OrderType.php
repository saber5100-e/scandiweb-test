<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Order',
            'fields' => [
                'id' => Type::int(),
                'total_amount' => Type::nonNull(Type::float()),
                'created_at' => Type::nonNull(Type::string())
            ]
        ]);
    }
}
