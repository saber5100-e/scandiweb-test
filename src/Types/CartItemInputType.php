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
                'product_name' => Type::string(),
                'id' => Type::string(),
                'quantity' => Type::int(),
                'amount' => Type::float()
            ]
        ]);
    }
}
