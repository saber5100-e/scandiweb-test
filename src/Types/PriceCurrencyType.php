<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceCurrencyType extends ObjectType {
    public function __construct()
    {
        parent::__construct([
            'name' => 'priceCurrency',
            'fields' => [
                'ID' => Type::int(),
                'Label' => type::string(),
                'Symbol' => type::string(),
                '__typename' => Type::string()
            ]
        ]);
    }
}