<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'             => 'ANKUR50',
                'type'             => 'fixed',
                'value'            => 50.00,
                'min_order_amount' => 199.00,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addDays(60),
            ],
            [
                'code'             => 'FIRST10',
                'type'             => 'percent',
                'value'            => 10.00, // 10% Off
                'min_order_amount' => 149.00,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addDays(30),
            ],
            [
                'code'             => 'FREESHIP',
                'type'             => 'fixed',
                'value'            => 25.00,
                'min_order_amount' => 99.00,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addDays(90),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}