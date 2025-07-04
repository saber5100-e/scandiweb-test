<?php

namespace App\Controller;

use App\Types\ProductsType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
Class Products {
    public static function handleProducts(){
        try {
            $productsType = new ProductsType();

            $queryType = new ObjectType([
                "name" => "Query",
                "fields" => [
                    "product" => [
                        "type" => $productsType,
                        "args" => [
                            "id" => Type::string()
                        ],
                        'resolve' => static fn($rootValue, $args): array => self::resolveProduct($args["id"])
                    ]
                ]
            ]);

            $schema = new Schema(
                (new SchemaConfig())->setQuery($queryType)
            );

            $rawInput = file_get_contents('php://input');

            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
             $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }

    public static function resolveProduct($id){
        $conn = mysqli_connect("localhost", "root", "", "scandiweb-test");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Products WHERE ID = '" . $id . "'";
        $productsResult = $conn->query($sql);
        $row = mysqli_fetch_assoc($productsResult);

        $gallery = [];
        $galleryResult = $conn->query("SELECT * FROM Products_gallery WHERE Product_ID='" . $id . "'");

        if ($galleryResult->num_rows > 0) {
            while($galleryRow = $galleryResult->fetch_assoc()) {
                array_push($gallery,$galleryRow);
            }
        }

        $attributes = [];
        $attributesResult = $conn->query("SELECT * FROM Products_Attributes WHERE Product_ID='" . $id . "'");

        if ($attributesResult->num_rows > 0) {
            while($attributesRow = $attributesResult->fetch_assoc()) {
                $items = [];
                $itemsResult = $conn->query("SELECT * FROM Attribute_Items WHERE Attribute_ID = '" . $attributesRow["Primary_ID"] . "'");

                if ($itemsResult->num_rows > 0) {
                    while($itemsRow = $itemsResult->fetch_assoc()) {
                        array_push($items,$itemsRow);
                    }
                }

                $attributesRow["Attributes_Items"] = $items;
                array_push($attributes,$attributesRow);
            }
        }

        $prices = [];
        $pricesResult = $conn->query("SELECT * FROM Product_Prices WHERE Product_ID='" . $id . "'");

        if ($pricesResult->num_rows > 0) {
            while($pricesRow = $pricesResult->fetch_assoc()){
                array_push($prices, $pricesRow);
            }
        }

        $row["Products_gallery"] = $gallery;
        $row["Products_Attributes"] = $attributes;
        $row["Product_Prices"] = $prices;

        $conn->close();
        return $row;
    }
}