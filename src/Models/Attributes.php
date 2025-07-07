<?php

namespace App\Models;
use App\Models\AttributesModel;
use mysqli;

class Attributes extends AttributesModel {
    public static function getAttributes(string $productId, mysqli $conn): array {
        $attributes = [];

        $stmt = $conn->prepare("SELECT * FROM Products_Attributes WHERE Product_ID = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $attributesResult = $stmt->get_result();

        while ($attrRow = $attributesResult->fetch_assoc()) {
            $attrId = $attrRow["Primary_ID"];
            $items = [];

            $itemStmt = $conn->prepare("SELECT * FROM Attribute_Items WHERE Attribute_ID = ?");
            $itemStmt->bind_param("s", $attrId);
            $itemStmt->execute();
            $itemsResult = $itemStmt->get_result();

            while ($itemRow = $itemsResult->fetch_assoc()) {
                $items[] = $itemRow;
            }

            $itemStmt->close();
            $attrRow["Attributes_Items"] = $items;
            $attributes[] = $attrRow;
        }

        $stmt->close();
        return $attributes;
    }
}