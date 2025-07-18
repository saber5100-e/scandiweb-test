<?php

namespace App;

class PopulateDB
{
    private $conn;
    private array $errors = [];

    public function __construct()
    {
        $servername = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $dbname = $_ENV['DB_NAME'];

        $this->conn = new \mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            throw new \RuntimeException("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function populate(): void
    {
        $this->createTables();
        $this->populateDB();
        $this->printErrors();
        $this->conn->close();
    }

    private function createTables()
    {
        $orders_table = "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            total_amount DECIMAL(10,2),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB";

        $categories_table = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(255),
            __typename VARCHAR(55)
        ) ENGINE=InnoDB";

        $products_table = "CREATE TABLE IF NOT EXISTS products (
            id VARCHAR(255) PRIMARY KEY,
            product_name VARCHAR(255),
            in_stock BOOLEAN,
            description TEXT,
            category VARCHAR(55),
            brand VARCHAR(100),
            __typename VARCHAR(55)
        ) ENGINE=InnoDB";

        $gallery_table = "CREATE TABLE IF NOT EXISTS products_gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255),
            url TEXT,
            FOREIGN KEY (product_id) REFERENCES products(id)
        ) ENGINE=InnoDB";

        $attributes_table = "CREATE TABLE IF NOT EXISTS products_attributes (
            primary_id INT AUTO_INCREMENT PRIMARY KEY,
            id VARCHAR(55),
            product_id VARCHAR(255),
            attribute_name VARCHAR(55),
            attribute_type VARCHAR(55),
            __typename VARCHAR(55),
            FOREIGN KEY (product_id) REFERENCES products(id)
        ) ENGINE=InnoDB";

        $items_table = "CREATE TABLE IF NOT EXISTS attribute_items (
            primary_id INT AUTO_INCREMENT PRIMARY KEY,
            id VARCHAR(55),
            attribute_id INT,
            display_value VARCHAR(25),
            item_value VARCHAR(25),
            __typename VARCHAR(25),
            FOREIGN KEY (attribute_id) REFERENCES products_attributes(primary_id)
        ) ENGINE=InnoDB";

        $currencies_table = "CREATE TABLE IF NOT EXISTS products_currnecy (
            id INT AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(5),
            symbol VARCHAR(3),
            __typename VARCHAR(25)
        ) ENGINE=InnoDB";

        $prices_table = "CREATE TABLE IF NOT EXISTS product_prices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255),
            currency_id INT,
            amount DECIMAL(10,2),
            __typename VARCHAR(25),
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (currency_id) REFERENCES products_currnecy(id)
        ) ENGINE=InnoDB";

        $tables = [
            $orders_table,
            $categories_table,
            $products_table,
            $gallery_table,
            $attributes_table,
            $items_table,
            $currencies_table,
            $prices_table
        ];

        foreach ($tables as $sql) {
            if (!$this->conn->query($sql)) {
                $this->errors[] = "Table creation failed: " . $this->conn->error;
            }
        }
    }

    private function populateDB()
    {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM categories");
        $row = $result->fetch_assoc();
        if ($row["count"] > 0) {
            return;
        }

        $json_data = file_get_contents(__DIR__ . '/../data.json');
        $data = json_decode($json_data, JSON_OBJECT_AS_ARRAY);

        $categories_query = $this->conn->prepare(
            "INSERT INTO categories(category_name, __typename) VALUES (?,?)"
        );
        $categories_query->bind_param("ss", $category_name, $category__typename);

        foreach ($data['data']['categories'] as $category) {
            $category_name = $category["name"];
            $category__typename = $category["__typename"];
            $categories_query->execute();
        }
        $categories_query->close();

        $products_query = $this->conn->prepare(
            "INSERT INTO products(
                id,
                product_name,
                in_stock,
                description,
                category,
                brand,
                __typename
            ) VALUES (?,?,?,?,?,?,?)"
        );
        $products_query->bind_param(
            "ssissss",
            $product_id,
            $product_name,
            $in_stock,
            $description,
            $category,
            $brand,
            $product__typename
        );

        foreach ($data['data']['products'] as $product) {
            $product_id = $product["id"];
            $product_name = $product["name"];
            $in_stock = $product["inStock"];
            $description = $product["description"];
            $category = $product["category"];
            $brand = $product["brand"];
            $product__typename = $product["__typename"];

            $products_query->execute();

            $gallery_query = $this->conn->prepare("INSERT INTO products_gallery(product_id, URL) VALUES (?,?)");
            $gallery_query->bind_param("ss", $product_id, $url);
            foreach ($product["gallery"] as $url) {
                $gallery_query->execute();
            }
            $gallery_query->close();

            $attribute_query = $this->conn->prepare(
                "INSERT INTO products_attributes (
                    id,
                    product_id,
                    attribute_name,
                    attribute_type,
                    __typename
                ) VALUES (?,?,?,?,?)"
            );
            $attribute_query->bind_param(
                "sssss",
                $attribute_id,
                $product_id,
                $attribute_name,
                $attribute_type,
                $attribute__typename
            );

            foreach ($product["attributes"] as $attribute) {
                $attribute_id = $attribute["id"];
                $attribute_name = $attribute["name"];
                $attribute_type = $attribute["type"];
                $attribute__typename = $attribute["__typename"];
                $attribute_query->execute();
                $attr_id = $this->conn->insert_id;

                $item_query = $this->conn->prepare(
                    "INSERT INTO attribute_items (
                        id,
                        attribute_id,
                        display_value,
                        item_value,
                        __typename
                    ) VALUES (?,?,?,?,?)"
                );
                $item_query->bind_param("sisss", $item_id, $attr_id, $display_value, $item_value, $item__typename);

                foreach ($attribute["items"] as $item) {
                    $item_id = $item["id"];
                    $display_value = $item["displayValue"];
                    $item_value = $item["value"];
                    $item__typename = $item["__typename"];
                    $item_query->execute();
                }

                $item_query->close();
            }
            $attribute_query->close();

            $price_query = $this->conn->prepare(
                "INSERT INTO product_prices (
                    product_id,
                    currency_ID,
                    amount,
                    __typename
                ) VALUES (?,?,?,?)"
            );
            $price_query->bind_param("sids", $product_id, $currency_id, $amount, $price__typename);

            $currency_insert = $this->conn->prepare("
                INSERT INTO products_currnecy (label, symbol, __typename)
                VALUES (?, ?, ?)
            ");
            $currency_insert->bind_param("sss", $currency_label, $currency_symbol, $currency__typename);

            foreach ($product["prices"] as $price) {
                $currency_label = $price["currency"]["label"];
                $currency_symbol = $price["currency"]["symbol"];
                $currency__typename = $price["currency"]["__typename"];

                $currency_insert->execute();
                $currency_id = $this->conn->insert_id;

                $amount = $price["amount"];
                $price__typename = $price["__typename"];
                $price_query->execute();
            }

            $price_query->close();
            $currency_insert->close();
        }

        $products_query->close();
    }

    private function printErrors()
    {
        if ($_ENV["APP_ENV"] ?? 'production' === 'development') {
            foreach ($this->errors as $error) {
                echo $error . "<br>";
            }
        }
    }
}
