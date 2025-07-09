<?php

namespace App\Models;

class ClothingProduct extends ProductModel
{
    public function toArray(): array
    {
        return [
            'ID' => $this->id,
            'Product_Name' => $this->productName,
            'In_Stock' => $this->in_stock,
            'Description' => $this->description,
            'Category' => $this->category,
            'Brand' => $this->brand,
            'Products_gallery' => $this->gallery,
            'Products_Attributes' => $this->attributes,
            'Product_Prices' => $this->prices,
            '__typename' => $this->typeName,
        ];
    }
}
