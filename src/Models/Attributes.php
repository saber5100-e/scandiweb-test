<?php

namespace App\Models;

use App\Models\AttributesModel;
use mysqli;

class Attributes extends AttributesModel
{
    public static function getAttributes(string $productId, mysqli $conn): array
    {
        $attributes = [];

        $stmt = $conn->prepare("SELECT * FROM products_attributes WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $attributesResult = $stmt->get_result();

        while ($attrRow = $attributesResult->fetch_assoc()) {
            $attrId = $attrRow["primary_id"];
            $items = [];

            $itemStmt = $conn->prepare("SELECT * FROM attribute_items WHERE attribute_id = ?");
            $itemStmt->bind_param("s", $attrId);
            $itemStmt->execute();
            $itemsResult = $itemStmt->get_result();

            while ($itemRow = $itemsResult->fetch_assoc()) {
                $items[] = $itemRow;
            }

            $itemStmt->close();
            $attrRow["attributes_items"] = $items;
            $attributes[] = $attrRow;
        }

        $stmt->close();
        return $attributes;
    }

    public static function fetchRawAttributes(string $productId, mysqli $conn): array
    {
        $stmt = $conn->prepare("SELECT * FROM products_attributes WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $attributesResult = $stmt->get_result();
        $attributes = [];
        while ($attrRow = $attributesResult->fetch_assoc()) {
            $attrId = $attrRow["primary_id"];
            $itemStmt = $conn->prepare("SELECT * FROM attribute_items WHERE attribute_id = ?");
            $itemStmt->bind_param("s", $attrId);
            $itemStmt->execute();
            $itemsResult = $itemStmt->get_result();
            $attrRow["attributes_items"] = [];
            while ($itemRow = $itemsResult->fetch_assoc()) {
                $attrRow["attributes_items"][] = $itemRow;
            }
            $itemStmt->close();
            $attributes[] = $attrRow;
        }
        $stmt->close();
        return $attributes;
    }
}
