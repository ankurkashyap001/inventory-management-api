<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of active categories in the exact target JSON structure.
     */
    public function index(): JsonResponse
    {
        // Sabhi active categories fetch ho rahi hain
        $categories = Category::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1000,
            'data' => [
                'sales_category' => CategoryResource::collection($categories),
                'prescription_upload_enable' => "0",
                'organisation_linking_enable' => false,
                'orgnization' => [
                    'connected' => false,
                ],
                'banner_list' => [],
                'deal_data' => [
                    [
                        'medicine_deal_text' => 'Deals of the week',
                        'medicine_deal_image_url' => 'https://images.stylight.net/image/upload/e_trim/t_web_product_330x440max_nobg/q_auto:eco,f_auto/nuihnmf62uddfcu3hmrs.jpg',
                        'deal_item' => 1,
                        'max_discount' => 0,
                    ]
                ],
                'top_deals' => [],
                'order_via_whatsapp' => '+1 (868) 434-5433',
            ]
        ], 200);
    }
}