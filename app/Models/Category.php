<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'prescription_required',
        'max_discount',
        'image',
        'banner_image',
        'logo_image',
        'logo_image_web',
        'is_active',
    ];

    protected $casts = [
        'prescription_required' => 'boolean',
        'max_discount' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Parent Category Relationship (Self-Referencing)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Sub-Categories Relationship
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Many-to-Many Relationship with Products
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    /* ================= Dynamic Image URL Accessors ================= */

    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner_image) return null;
        return filter_var($this->banner_image, FILTER_VALIDATE_URL) 
            ? $this->banner_image 
            : asset('storage/' . $this->banner_image);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_image) return null;
        return filter_var($this->logo_image, FILTER_VALIDATE_URL) 
            ? $this->logo_image 
            : asset('storage/' . $this->logo_image);
    }

    public function getLogoUrlWebAttribute(): ?string
    {
        if (!$this->logo_image_web) return null;
        return filter_var($this->logo_image_web, FILTER_VALIDATE_URL) 
            ? $this->logo_image_web 
            : asset('storage/' . $this->logo_image_web);
    }
}