<?php

namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Types\ProductsGalleryType;
use App\Types\ProductsAttributesType;
use App\Types\ProductPricesType;

class ProductsType extends ObjectType
{
    public function __construct()
    {
        $productsGalleryType = new ProductsGalleryType();
        $productsAttributesType = new ProductsAttributesType();
        $productPricesType = new ProductPricesType();

        parent::__construct([
            "name" => "Products",
            "fields" => [
                "id" => Type::nonNull(Type::string()),
                "product_name" => Type::nonNull(Type::string()),
                "in_stock" => Type::nonNull(Type::boolean()),
                "description" => Type::nonNull(Type::string()),
                "category" => Type::nonNull(Type::string()),
                "brand" => Type::nonNull(Type::string()),
                "products_gallery" => Type::listOf($productsGalleryType),
                "products_attributes" => Type::listOf($productsAttributesType),
                "product_prices" => Type::listOf($productPricesType),
                "__typename" => Type::nonNull(Type::string())
            ]
        ]);
    }
}
