<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Generate full image URL (handles local storage paths & external HTTP links)
        $imageUrl = null;
        if ($this->image) {
            $imageUrl = str_starts_with($this->image, 'http')
                ? $this->image
                : asset(Storage::url($this->image));
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'image'       => $this->image,
            'image_url'   => $imageUrl,
            'is_active'   => (bool) ($this->is_active ?? true),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}