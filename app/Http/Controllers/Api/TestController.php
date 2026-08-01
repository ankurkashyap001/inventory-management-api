<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function getData()
    {
        return response()->json([
            'status' => true,
            'message' => 'Laravel API connected successfully!',
            'data' => [
                'framework' => 'Laravel 11',
                'frontend' => 'Angular'
            ]
        ]);
    }
}