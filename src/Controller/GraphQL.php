<?php

namespace App\Controller;

use App\Models\ProductFactory;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use App\Types\ProductsType;
use App\Types\CategoriesType;
use App\Types\OrderType;
use App\Types\CartItemInputType;
use App\Database\Database;
use App\Models\Product;
use App\Models\Category;

class GraphQL
{
    public static function handle()
    {
        try {
            $productsType = new ProductsType();
            $categoriesType = new CategoriesType();
            $orderType = new OrderType();
            $cartItemInputType = new CartItemInputType();

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf($productsType),
                        'resolve' => fn() => array_map(fn($product) => $product->toArray(), Product::findAll()),
                    ],
                    'product' => [
                        'type' => $productsType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => fn($root, $args) =>
                            ($product = Product::findById($args['id'])) ? $product->toArray() : null,
                    ],
                    'category' => [
                        'type' => Type::listOf($productsType),
                        'args' => [
                            'Category_Name' => Type::string()
                        ],
                        'resolve' => fn($root, $args) => array_map(
                            fn($product) => $product->toArray(),
                            ProductFactory::findByCategoryOrAll($args['Category_Name'] ?? null)
                        ),
                    ],
                    'categories' => [
                        'type' => Type::listOf($categoriesType),
                        'resolve' => fn() => Category::findAll()
                    ]
                ]
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'order' => [
                        'type' => $orderType,
                        'args' => [
                            'input' => Type::listOf($cartItemInputType)
                        ],
                        'resolve' => fn($root, $args) => self::resolveOrder($args),
                    ]
                ]
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
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

    public static function resolveOrder($args)
    {
        $input_items = $args['input'];
        $total_amount = 0;

        foreach ($input_items as $item) {
            $total_amount += $item["Amount"] * $item["Quantity"];
        }

        $conn = Database::getConnection();
        if ($conn->connect_error) {
            throw new RuntimeException("Connection failed: " . $conn->connect_error);
        }

        $stat = $conn->prepare("INSERT INTO Orders (Total_Amount) VALUES (?)");
        $stat->bind_param('d', $total_amount);
        $stat->execute();

        $last_id = $conn->insert_id;

        $result = $conn->query("SELECT * FROM Orders WHERE ID = $last_id");
        $order = mysqli_fetch_assoc($result);
        $conn->close();

        return $order;
    }
}
