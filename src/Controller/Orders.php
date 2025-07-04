<?php
namespace App\Controller;

use App\Types\OrderType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Types\CartItemInputType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

Class Orders {
    public static function handleOrders() {
        try {
            $orderType = new OrderType();
            $cartItemInputType = new CartItemInputType();

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'order' => [
                        'type' => $orderType,
                        'args' => [
                            'input' => Type::listOf($cartItemInputType)
                        ],
                        'resolve' => static fn ($rootValue, $args): array => self::resolveOrder($args),
                    ],
                ],
            ]);
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                ->setMutation($mutationType)
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
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

    public static function resolveOrder($args) {
        $input_items = $args['input'];
        $total_amount = 0;
        
        foreach($input_items as $item){
            $total_amount += $item["Amount"] * $item["Quantity"];
        }
        
        $conn = mysqli_connect("localhost", "root", "", "scandiweb-test");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Orders (Total_Amount) VALUES (" . $total_amount . ")";
        $conn->query($sql);
        $last_id = $conn->insert_id;

        $result = $conn->query("SELECT * FROM Orders WHERE ID = " . $last_id);
        $order = mysqli_fetch_assoc($result);
        $conn->close();

        return $order;
    }
}