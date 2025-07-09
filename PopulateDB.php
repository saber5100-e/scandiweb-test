<?php
class PopulateDB {
    private $conn;
    private array $errors = [];

    public function __construct() {
        $servername = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $dbname = $_ENV['DB_NAME'];

        $this->conn = new \mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            throw new \RuntimeException("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function populate(): void {
        $this->createTables();
        $this->populateDB();
        $this->printErrors();
        $this->conn->close();
    }

    private function createTables() {
        $orders_table = "CREATE TABLE IF NOT EXISTS Orders (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Total_Amount DECIMAL(10,2),
            Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB";

        $categories_table = "CREATE TABLE IF NOT EXISTS Categories (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Category_Name VARCHAR(255),
            __typename VARCHAR(55)
        ) ENGINE=InnoDB";

        $products_table = "CREATE TABLE IF NOT EXISTS Products (
            ID VARCHAR(255) PRIMARY KEY,
            Product_Name VARCHAR(255),
            In_Stock BOOLEAN,
            Description TEXT,
            Category VARCHAR(55),
            Brand VARCHAR(100),
            __typename VARCHAR(55)
        ) ENGINE=InnoDB";

        $gallery_table = "CREATE TABLE IF NOT EXISTS Products_gallery (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Product_ID VARCHAR(255),
            URL TEXT,
            FOREIGN KEY (Product_ID) REFERENCES Products(ID)
        ) ENGINE=InnoDB";

        $attributes_table = "CREATE TABLE IF NOT EXISTS Products_Attributes (
            Primary_ID INT AUTO_INCREMENT PRIMARY KEY,
            ID VARCHAR(55),
            Product_ID VARCHAR(255),
            Attribute_Name VARCHAR(55),
            Attribute_Type VARCHAR(55),
            __typename VARCHAR(55),
            FOREIGN KEY (Product_ID) REFERENCES Products(ID)
        ) ENGINE=InnoDB";

        $items_table = "CREATE TABLE IF NOT EXISTS Attribute_Items (
            Primary_ID INT AUTO_INCREMENT PRIMARY KEY,
            ID VARCHAR(55),
            Attribute_ID INT,
            Display_Value VARCHAR(25),
            Item_Value VARCHAR(25),
            __typename VARCHAR(25),
            FOREIGN KEY (Attribute_ID) REFERENCES Products_Attributes(Primary_ID)
        ) ENGINE=InnoDB";

        $currencies_table = "CREATE TABLE IF NOT EXISTS Products_Currnecy (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Label VARCHAR(5),
            Symbol VARCHAR(3),
            __typename VARCHAR(25)
        ) ENGINE=InnoDB";

        $prices_table = "CREATE TABLE IF NOT EXISTS Product_Prices (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Product_ID VARCHAR(255),
            Currency_ID INT,
            Amount DECIMAL(10,2),
            __typename VARCHAR(25),
            FOREIGN KEY (Product_ID) REFERENCES Products(ID),
            FOREIGN KEY (Currency_ID) REFERENCES Products_Currnecy(ID)
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

    private function populateDB() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM Categories");
        $row = $result->fetch_assoc();
        if ($row["count"] > 0) return;

        $json_data = file_get_contents("data.json", FILE_USE_INCLUDE_PATH);
        $data = json_decode($json_data, JSON_OBJECT_AS_ARRAY);

        $categories_query = $this->conn->prepare(
            "INSERT INTO Categories(Category_Name, __typename) VALUES (?,?)"
        );
        $categories_query->bind_param("ss", $category_name, $category__typename);

        foreach ($data['data']['categories'] as $category) {
            $category_name = $category["name"];
            $category__typename = $category["__typename"];
            $categories_query->execute();
        }
        $categories_query->close();

        $products_query = $this->conn->prepare(
            "INSERT INTO Products(ID, Product_Name, In_Stock, Description, Category, Brand, __typename) VALUES (?,?,?,?,?,?,?)"
        );
        $products_query->bind_param("ssissss", $product_id, $product_name, $in_stock, $description, $category, $brand, $product__typename);

        foreach ($data['data']['products'] as $product) {
            $product_id = $product["id"];
            $product_name = $product["name"];
            $in_stock = $product["inStock"];
            $description = $product["description"];
            $category = $product["category"];
            $brand = $product["brand"];
            $product__typename = $product["__typename"];

            $products_query->execute();

            $gallery_query = $this->conn->prepare("INSERT INTO Products_gallery(Product_ID, URL) VALUES (?,?)");
            $gallery_query->bind_param("ss", $product_id, $url);
            foreach ($product["gallery"] as $url) {
                $gallery_query->execute();
            }
            $gallery_query->close();

            $attribute_query = $this->conn->prepare(
                "INSERT INTO Products_Attributes (ID, Product_ID, Attribute_Name, Attribute_Type, __typename) VALUES (?,?,?,?,?)"
            );
            $attribute_query->bind_param("sssss", $attribute_id, $product_id, $attribute_name, $attribute_type, $attribute__typename);

            foreach ($product["attributes"] as $attribute) {
                $attribute_id = $attribute["id"];
                $attribute_name = $attribute["name"];
                $attribute_type = $attribute["type"];
                $attribute__typename = $attribute["__typename"];
                $attribute_query->execute();
                $attr_id = $this->conn->insert_id;

                $item_query = $this->conn->prepare(
                    "INSERT INTO Attribute_Items (ID, Attribute_ID, Display_Value, Item_Value, __typename) VALUES (?,?,?,?,?)"
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
                "INSERT INTO Product_Prices (Product_ID, Currency_ID, Amount, __typename) VALUES (?,?,?,?)"
            );
            $price_query->bind_param("sids", $product_id, $currency_id, $amount, $price__typename);

            $currency_insert = $this->conn->prepare("
                INSERT INTO Products_Currnecy (Label, Symbol, __typename)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE Symbol = VALUES(Symbol), __typename = VALUES(__typename)
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

    private function printErrors() {
        if ($_ENV["APP_ENV"] ?? 'production' === 'development') {
            foreach ($this->errors as $error) {
                echo $error . "<br>";
            }
        }
    }
}