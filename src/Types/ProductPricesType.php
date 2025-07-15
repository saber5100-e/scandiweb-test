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
                'id' => Type::nonNull(Type::int()),
                'amount' => Type::nonNull(Type::float()),
                '__typename' => Type::nonNull(Type::string()),
                'currency_id' => Type::nonNull(Type::int()),
                'currency' => Type::nonNull($priceCurrencyType)
            ]
        ]);
    }
}
