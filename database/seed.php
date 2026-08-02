<?php
/**
 * Seeder: run once after importing schema.sql.
 *   php database/seed.php
 * Creates the default admin, categories, brands, sample data and settings.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('STORAGE_PATH', BASE_PATH . '/storage');

require APP_PATH . '/Core/Helpers.php';
App\Core\Env::load(BASE_PATH . '/.env');

use App\Core\Database;

$pdo = Database::connection();
echo "Seeding ToolzyNet database...\n";

// ---- Admin user ----
$adminEmail = 'admin@toolzynet.com';
$adminPass  = 'Admin@12345';   // change after first login
$exists = Database::value('SELECT COUNT(*) FROM users WHERE email = ?', [$adminEmail]);
if (!$exists) {
    Database::insert(
        'INSERT INTO users (name, email, password, role, is_active, email_verified_at)
         VALUES (?, ?, ?, "admin", 1, NOW())',
        ['Site Admin', $adminEmail, password_hash($adminPass, PASSWORD_DEFAULT)]
    );
    echo "  ✓ Admin created: {$adminEmail} / {$adminPass}\n";
} else {
    echo "  • Admin already exists\n";
}

// ---- Categories ----
$icons = [
    'Mobiles' => 'fa-mobile-screen', 'Laptops' => 'fa-laptop', 'Smart Watches' => 'fa-clock',
    'Headphones' => 'fa-headphones', 'Cameras' => 'fa-camera', 'Computer Accessories' => 'fa-keyboard',
    'Gaming' => 'fa-gamepad', 'Home Appliances' => 'fa-blender', 'Kitchen' => 'fa-utensils',
    'Beauty' => 'fa-wand-magic-sparkles', 'Fashion' => 'fa-shirt', 'Shoes' => 'fa-shoe-prints',
    'Fitness' => 'fa-dumbbell', 'Health' => 'fa-heart-pulse', 'Books' => 'fa-book',
    'Baby Products' => 'fa-baby', 'Office Products' => 'fa-briefcase',
    'Automobile Accessories' => 'fa-car', 'Pet Products' => 'fa-paw',
];
$order = 0;
foreach (config('app.default_categories') as $name) {
    $slug = slugify($name);
    $has = Database::value('SELECT COUNT(*) FROM categories WHERE slug = ?', [$slug]);
    if (!$has) {
        Database::insert(
            'INSERT INTO categories (name, slug, icon, sort_order, is_active, meta_title, meta_description)
             VALUES (?, ?, ?, ?, 1, ?, ?)',
            [$name, $slug, $icons[$name] ?? 'fa-tag', $order++,
             "$name - Best Deals & Reviews | ToolzyNet",
             "Browse the best $name with reviews, comparisons and top affiliate deals on ToolzyNet."]
        );
    }
}
echo "  ✓ Categories seeded\n";

// ---- Brands ----
foreach (['Apple', 'Samsung', 'Sony', 'boAt', 'OnePlus', 'HP', 'Dell', 'Nike', 'Puma', 'Realme'] as $b) {
    $slug = slugify($b);
    $has = Database::value('SELECT COUNT(*) FROM brands WHERE slug = ?', [$slug]);
    if (!$has) {
        Database::insert('INSERT INTO brands (name, slug, is_active) VALUES (?, ?, 1)', [$b, $slug]);
    }
}
echo "  ✓ Brands seeded\n";

// ---- Settings ----
$settings = [
    ['site_name', 'ToolzyNet', 'general'],
    ['tagline', 'Compare. Review. Buy Smart.', 'general'],
    ['contact_email', 'hello@toolzynet.com', 'general'],
    ['footer_copyright', '© ' . date('Y') . ' ToolzyNet.com — All rights reserved.', 'general'],
    ['theme_color', '#2563eb', 'appearance'],
    ['dark_mode', '0', 'appearance'],
    ['maintenance_mode', '0', 'general'],
    ['meta_title', 'ToolzyNet — Product Reviews, Comparisons & Best Deals', 'seo'],
    ['meta_description', 'ToolzyNet helps you compare products, read honest reviews and find the best affiliate deals from Amazon, Flipkart, Meesho and more.', 'seo'],
    ['social_facebook', '', 'social'],
    ['social_twitter', '', 'social'],
    ['social_instagram', '', 'social'],
    ['social_youtube', '', 'social'],
];
foreach ($settings as [$k, $v, $g]) {
    Database::run(
        'INSERT INTO settings (`key`, `value`, `group`) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
        [$k, $v, $g]
    );
}
echo "  ✓ Settings seeded\n";

// ---- Sample products ----
$mobiles = Database::value('SELECT id FROM categories WHERE slug = "mobiles"');
$apple   = Database::value('SELECT id FROM brands WHERE slug = "apple"');
if ($mobiles && !Database::value('SELECT COUNT(*) FROM products')) {
    $pid = Database::insert(
        'INSERT INTO products
         (category_id, brand_id, name, slug, short_description, long_description, features, specifications, pros, cons, faq,
          rating, rating_count, price, original_price, discount, amazon_link, flipkart_link, status, published_at,
          is_featured, is_trending, is_deal, is_bestseller, is_new, meta_title, meta_description)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "published", NOW(), 1,1,1,1,1, ?, ?)',
        [
            $mobiles, $apple,
            'Apple iPhone 16 Pro', 'apple-iphone-16-pro',
            'The most advanced iPhone with A18 Pro chip and titanium design.',
            '<p>The iPhone 16 Pro pushes performance and camera quality to new heights with a titanium frame, the A18 Pro chip, and an advanced 48MP camera system.</p>',
            json_encode(['A18 Pro chip', '48MP Fusion camera', 'Titanium body', 'USB-C', '120Hz ProMotion']),
            json_encode(['Display' => '6.3" OLED 120Hz', 'Chip' => 'A18 Pro', 'RAM' => '8GB', 'Storage' => '256GB', 'Battery' => '3600mAh']),
            json_encode(['Excellent cameras', 'Premium build', 'Fast performance']),
            json_encode(['Expensive', 'No charger in box']),
            json_encode([['q' => 'Does it support 5G?', 'a' => 'Yes, the iPhone 16 Pro supports 5G networks.'], ['q' => 'Is it waterproof?', 'a' => 'It has IP68 water and dust resistance.']]),
            4.7, 128, 119900, 134900, 11,
            'https://www.amazon.in/', 'https://www.flipkart.com/',
            'Apple iPhone 16 Pro Review, Price & Best Deals | ToolzyNet',
            'Full review, specs, pros & cons and the best price for the Apple iPhone 16 Pro on ToolzyNet.',
        ]
    );
    Database::insert(
        'INSERT INTO product_images (product_id, path, alt, is_primary, sort_order) VALUES (?, ?, ?, 1, 0)',
        [$pid, 'products/placeholder-phone.png', 'Apple iPhone 16 Pro']
    );
    echo "  ✓ Sample product created\n";
}

echo "Done. Log in at /admin/login\n";
