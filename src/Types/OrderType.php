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
                'ID' => Type::int(),
                'Total_Amount' => Type::float(),
                'Created_At' => Type::string()
            ]
        ]);
    }
}
