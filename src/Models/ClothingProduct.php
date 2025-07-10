<?php

namespace App\Models;

class ClothingProduct extends ProductModel
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->productName,
            'in_stock' => $this->inStock,
            'description' => $this->description,
            'category' => $this->category,
            'brand' => $this->brand,
            'products_gallery' => $this->gallery,
            'products_attributes' => $this->attributes,
            'product_prices' => $this->prices,
            '__typename' => $this->typeName,
        ];
    }
}
