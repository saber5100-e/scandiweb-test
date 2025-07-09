<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductPricesType extends ObjectType
{
    public function __construct()
    {
        $priceCurrencyType = new PriceCurrencyType();

        parent::__construct([
            'name' => 'ProductPrices',
            'fields' => [
                'id' => Type::int(),
                'currency' => $priceCurrencyType,
                'amount' => Type::float(),
                '__typename' => Type::string()
            ]
        ]);
    }
}
