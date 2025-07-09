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
                'Product_name' => Type::string(),
                'ID' => Type::string(),
                'Quantity' => Type::int(),
                'Amount' => Type::float()
            ]
        ]);
    }
}
