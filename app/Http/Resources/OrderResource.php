<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id'         => $this->id,
            'order_number'     => $this->order_number,
            'total_amount'     => (float) $this->total_amount,
            'status'           => $this->status,
            'payment_method'   => $this->payment_method,
            'payment_status'   => $this->payment_status,
            'shipping_address' => $this->shipping_address_json,
            'items_count'      => $this->items->sum('quantity'),
            'items'            => $this->items->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'product_id'  => $item->product_id,
                    'title'       => $item->product?->title ?? 'Product Removed',
                    'unit_price'  => (float) $item->unit_price,
                    'quantity'    => $item->quantity,
                    'total_price' => (float) $item->total_price,
                    'image_url'   => $item->product?->primaryImage?->image_path,
                ];
            }),
            'created_at'       => $this->created_at->toIso8601String(),
        ];
    }
}