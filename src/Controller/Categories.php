<?php

namespace App\Controller;

use App\Types\ProductsType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\Types\CategoriesType;
use RuntimeException;
use Throwable;

Class Categories {
    public static function handleCategories(){
        try {
            $categoryType = new CategoriesType();
            $productsType = new ProductsType();

            $queryType = new ObjectType([
                "name" => "Query",
                "fields" => [
                    "categories" => [
                        "type" => Type::listOf($categoryType),
                        'resolve' => static fn(): array => self::resolveCategories()
                    ],
                    "products" => [
                        "type" => Type::listOf($productsType),
                        'resolve' => static fn(): array => self::resolveProducts()
                    ],
                    "category" => [
                        "type" => Type::listOf($productsType),
                        "args" => [
                            "Category_Name" => Type::string()
                        ],
                        "resolve" => static fn($rootValue, array $args): array => self::resolveCategory($args["Category_Name"])
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

    public static function resolveCategories(){
        $conn = mysqli_connect("localhost", "root", "", "scandiweb-test");
        $categories = [];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Categories";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($categories, $row);
        }
        } else {
            echo "0 results";
        }
        $conn->close();

        return $categories;
    }

    public static function resolveProducts() {
        $conn = mysqli_connect("localhost", "root", "", "scandiweb-test");
        $products = [];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $gallery = [];
                $galleryResult = $conn->query("SELECT * FROM Products_gallery WHERE Product_ID='" . $row["ID"] . "'");

                if ($galleryResult->num_rows > 0) {
                    while ($galleryRow = $galleryResult->fetch_assoc()) {
                        array_push($gallery, $galleryRow);
                    }
                }

                $row["Products_gallery"] = $gallery;

                $attributes = [];
                $attributesResult = $conn->query("SELECT * FROM Products_Attributes WHERE Product_ID='" . $row['ID'] . "'");

                if ($attributesResult->num_rows > 0) {
                    while ($attributesRow = $attributesResult->fetch_assoc()) {
                        $items = [];
                        $itemsResult = $conn->query("SELECT * FROM Attribute_Items WHERE Attribute_ID='" . $attributesRow["Primary_ID"] . "'");
                        if ($itemsResult->num_rows > 0) {
                            while ($itemRow = $itemsResult->fetch_assoc()) {
                                array_push($items, $itemRow);
                            }
                        }
                    }
                }

                $row["Products_Attributes"] = $attributes;

                $prices = [];
                $pricesResult = $conn->query("SELECT * FROM Product_Prices WHERE Product_ID='" . $row['ID'] . "'");

                if ($pricesResult->num_rows > 0) {
                    while ($priceRow = $pricesResult->fetch_assoc()) {
                        array_push($prices, $priceRow);
                    }
                }
                $row["Product_Prices"] = $prices;

                $products[] = $row;
            }
        }

        $conn->close();
        return $products;
    }

    public static function resolveCategory($category_name) {
        $conn = mysqli_connect("localhost", "root", "", "scandiweb-test");
        $products = [];
        $sql = "";

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if($category_name != "All"){
            $sql = "SELECT * FROM Products WHERE category = '" . $category_name . "'";
        } else {
            $sql = "SELECT * FROM Products";
        }
        
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $gallery = [];
                $galleryResult = $conn->query("SELECT * FROM Products_gallery WHERE Product_ID='" . $row["ID"] . "'");

                if ($galleryResult->num_rows > 0) {
                    while ($galleryRow = $galleryResult->fetch_assoc()) {
                        array_push($gallery, $galleryRow);
                    }
                }

                $row["Products_gallery"] = $gallery;

                $attributes = [];
                $attributesResult = $conn->query("SELECT * FROM Products_Attributes WHERE Product_ID='" . $row['ID'] . "'");

                if ($attributesResult->num_rows > 0) {
                    while ($attributesRow = $attributesResult->fetch_assoc()) {
                        $items = [];
                        $itemsResult = $conn->query("SELECT * FROM Attribute_Items WHERE Attribute_ID='" . $attributesRow["Primary_ID"] . "'");
                        if ($itemsResult->num_rows > 0) {
                            while ($itemRow = $itemsResult->fetch_assoc()) {
                                array_push($items, $itemRow);
                            }
                        }

                        $attributesRow["Attributes_Items"] = $items;
                        $attributes[] = $attributesRow;
                    }
                }

                $row["Products_Attributes"] = $attributes;

                $prices = [];
                $pricesResult = $conn->query("SELECT * FROM Product_Prices WHERE Product_ID='" . $row['ID'] . "'");

                if ($pricesResult->num_rows > 0) {
                    while ($priceRow = $pricesResult->fetch_assoc()) {
                        array_push($prices, $priceRow);
                    }
                }
                $row["Product_Prices"] = $prices;

                $products[] = $row;
            }
        }

        $conn->close();
        return $products;
    }
}