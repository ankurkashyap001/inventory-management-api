<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'prescription_required' => $this->prescription_required ? "1" : "0",
            'category_id' => (string) $this->id,
            'category_name' => $this->name,
            'cat_desc' => $this->description,
            'max_discount' => (string) $this->max_discount,
            'category_banner_url' => $this->banner_url,
            'category_description' => $this->description,
            'category_logo_url' => $this->logo_url,
            'category_logo_url_web' => $this->logo_url_web,
            'parent_id' => $this->parent_id ? (string) $this->parent_id : null,
        ];
    }
}