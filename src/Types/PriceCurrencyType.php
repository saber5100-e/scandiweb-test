<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceCurrencyType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'priceCurrency',
            'fields' => [
                'id' => Type::int(),
                'label' => type::string(),
                'symbol' => type::string(),
                '__typename' => Type::string()
            ]
        ]);
    }
}
