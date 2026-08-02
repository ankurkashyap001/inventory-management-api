<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate discount percentage if sale_price exists
        $discountPercentage = null;
        if ($this->sale_price && $this->price > 0) {
            $discount = $this->price - $this->sale_price;
            $discountPercentage = round(($discount / $this->price) * 100);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'discount_percentage' => $discountPercentage,
            'stock_quantity' => $this->stock_quantity,
            'in_stock' => $this->stock_quantity > 0,
            'sku' => $this->sku,
            'prescription_required' => (bool) $this->prescription_required,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_path,
                    'is_primary' => (bool) $image->is_primary,
                ];
            }),
            'primary_image' => $this->primaryImage ? $this->primaryImage->image_path : null,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}