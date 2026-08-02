<?php

return [
    'name'        => env('APP_NAME', 'ToolzyNet'),
    'url'         => env('APP_URL', 'http://localhost'),
    'env'         => env('APP_ENV', 'production'),
    'debug'       => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
    'timezone'    => env('APP_TIMEZONE', 'Asia/Kolkata'),

    'per_page'    => 12,
    'blog_per_page' => 9,

    'default_categories' => [
        'Mobiles', 'Laptops', 'Smart Watches', 'Headphones', 'Cameras',
        'Computer Accessories', 'Gaming', 'Home Appliances', 'Kitchen', 'Beauty',
        'Fashion', 'Shoes', 'Fitness', 'Health', 'Books', 'Baby Products',
        'Office Products', 'Automobile Accessories', 'Pet Products',
    ],

    'affiliate_partners' => ['amazon', 'flipkart', 'meesho', 'custom'],
];
