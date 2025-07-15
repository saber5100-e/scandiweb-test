<?php

namespace App\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class CartItemInputType extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'CartItemInput',
            'fields' => [
                'product_name' => Type::nonNull(Type::string()),
                'id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'amount' => Type::nonNull(Type::float())
            ]
        ]);
    }
}
