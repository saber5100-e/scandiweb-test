<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductsGalleryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'ProductsGallery',
            'fields' => [
                'URL' => Type::string(),
                'ID' => Type::int(),
                'Product_ID' => Type::string(),
            ]
        ]);
    }
}
