<?php

namespace App\Models;

use App\Database\Database;
use RuntimeException;
use App\Models\CategoryModel;

class Category extends CategoryModel
{
    public static function findAll(): array
    {
        $conn = Database::getConnection();
        if ($conn->connect_error) {
            throw new RuntimeException("Connection failed: " . $conn->connect_error);
        }
        $categories = [];
        $sql = "SELECT * FROM Categories";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($categories, $row);
            }
        }
        $conn->close();
        return $categories;
    }
}
