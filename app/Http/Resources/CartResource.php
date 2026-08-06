<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cart_id'      => $this->id,
            'total_amount' => (float) $this->total_amount,
            'total_items'  => $this->items->sum('quantity'),
            'items'        => $this->items->map(function ($item) {
                return [
                    'item_id'       => $item->id,
                    'product_id'    => $item->product_id,
                    'title'         => $item->product->title,
                    'price'         => (float) $item->price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => (float) $item->subtotal,
                    'stock_available' => $item->product->stock_quantity,
                    'image_url'     => $item->product->primaryImage?->image_path 
                                       ?? $item->product->images->first()?->image_path,
                ];
            }),
        ];
    }
}