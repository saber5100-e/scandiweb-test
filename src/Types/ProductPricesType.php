<?php
namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductPricesType extends ObjectType {
    public function __construct()
    {
        parent::__construct([
            'name' => 'ProductPrices',
            'fields' => [
                'ID' => Type::int(),
                'Product_ID' => Type::string(),
                'Currency_Label' => Type::string(),
                'Currency_Symbol' => Type::string(),
                'Currency__Typename' => Type::string(),
                'Amount' => Type::float(),
                '__typename' => Type::string()
            ]
        ]);
    }
}