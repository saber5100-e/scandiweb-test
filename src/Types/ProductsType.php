<?php
namespace App\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Types\ProductsGalleryType;
use App\Types\ProductsAttributesType;
use App\Types\ProductPricesType;

class ProductsType extends ObjectType {
    public function __construct(){
        $productsGalleryType = new ProductsGalleryType();
        $productsAttributesType = new ProductsAttributesType();
        $productPricesType = new ProductPricesType();

        parent::__construct([
            "name" => "Products",
                "fields" => [
                    "ID" => Type::string(),
                    "Product_Name" => Type::string(),
                    "In_Stock" => type::boolean(),
                    "Description" => Type::string(),
                    "Category" => Type::string(),
                    "Brand" => Type::string(),
                    "Products_gallery" => Type::listOf($productsGalleryType),
                    "Products_Attributes" => Type::listOf($productsAttributesType),
                    "Product_Prices" => Type::listOf($productPricesType),
                    "__typename" => Type::string()
                ]
        ]);
    }
}