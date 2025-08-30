<?php
return [
    'points_per_rupiah' => 1 / 10000,     // 1 poin / Rp10.000 (earn)
    'pointable_status'  => ['paid','completed','delivered'],
    'min_total_price_for_points' => 0,

    'points' => [
        'conversion_value'      => 100,   // REDEEM: 1 poin = Rp100
        'min_redeem'            => 100,   // minimal tebus
        'max_percentage_discount'=> 50,   // maksimal 50% dari total
    ],
    'shipping' => [
        'regular' => 15000,
        'express' => 25000,
    ],
];
