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
                'id' => Type::nonNull(Type::int()),
                'label' => Type::nonNull(Type::string()),
                'symbol' => Type::nonNull(Type::string()),
                '__typename' => Type::nonNull(Type::string())
            ]
        ]);
    }
}
