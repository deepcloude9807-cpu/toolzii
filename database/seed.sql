-- =====================================================================
-- ToolzyNet — Seed data (import AFTER schema.sql via phpMyAdmin)
-- Use this on shared hosting (Hostinger) where you can't run seed.php.
-- Default admin login:  admin@toolzynet.com  /  Admin@12345
-- >>> Change this password right after your first login. <<<
-- =====================================================================

-- ---- Admin user ----
INSERT INTO users (name, email, password, role, is_active, email_verified_at)
SELECT 'Site Admin', 'admin@toolzynet.com',
       '$2y$12$6IGC3oEzCa1ILEOEprg.fe/tDuWXhkBU.AZ1pcQ5zaTomjCd6Beh6',
       'admin', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@toolzynet.com');

-- ---- Categories ----
INSERT INTO categories (name, slug, icon, sort_order, is_active, meta_title, meta_description) VALUES
('Mobiles','mobiles','fa-mobile-screen',0,1,'Mobiles - Best Deals & Reviews | ToolzyNet','Browse the best Mobiles with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Laptops','laptops','fa-laptop',1,1,'Laptops - Best Deals & Reviews | ToolzyNet','Browse the best Laptops with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Smart Watches','smart-watches','fa-clock',2,1,'Smart Watches - Best Deals & Reviews | ToolzyNet','Browse the best Smart Watches with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Headphones','headphones','fa-headphones',3,1,'Headphones - Best Deals & Reviews | ToolzyNet','Browse the best Headphones with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Cameras','cameras','fa-camera',4,1,'Cameras - Best Deals & Reviews | ToolzyNet','Browse the best Cameras with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Computer Accessories','computer-accessories','fa-keyboard',5,1,'Computer Accessories - Best Deals & Reviews | ToolzyNet','Browse the best Computer Accessories with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Gaming','gaming','fa-gamepad',6,1,'Gaming - Best Deals & Reviews | ToolzyNet','Browse the best Gaming gear with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Home Appliances','home-appliances','fa-blender',7,1,'Home Appliances - Best Deals & Reviews | ToolzyNet','Browse the best Home Appliances with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Kitchen','kitchen','fa-utensils',8,1,'Kitchen - Best Deals & Reviews | ToolzyNet','Browse the best Kitchen products with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Beauty','beauty','fa-wand-magic-sparkles',9,1,'Beauty - Best Deals & Reviews | ToolzyNet','Browse the best Beauty products with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Fashion','fashion','fa-shirt',10,1,'Fashion - Best Deals & Reviews | ToolzyNet','Browse the best Fashion with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Shoes','shoes','fa-shoe-prints',11,1,'Shoes - Best Deals & Reviews | ToolzyNet','Browse the best Shoes with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Fitness','fitness','fa-dumbbell',12,1,'Fitness - Best Deals & Reviews | ToolzyNet','Browse the best Fitness gear with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Health','health','fa-heart-pulse',13,1,'Health - Best Deals & Reviews | ToolzyNet','Browse the best Health products with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Books','books','fa-book',14,1,'Books - Best Deals & Reviews | ToolzyNet','Browse the best Books with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Baby Products','baby-products','fa-baby',15,1,'Baby Products - Best Deals & Reviews | ToolzyNet','Browse the best Baby Products with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Office Products','office-products','fa-briefcase',16,1,'Office Products - Best Deals & Reviews | ToolzyNet','Browse the best Office Products with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Automobile Accessories','automobile-accessories','fa-car',17,1,'Automobile Accessories - Best Deals & Reviews | ToolzyNet','Browse the best Automobile Accessories with reviews, comparisons and top affiliate deals on ToolzyNet.'),
('Pet Products','pet-products','fa-paw',18,1,'Pet Products - Best Deals & Reviews | ToolzyNet','Browse the best Pet Products with reviews, comparisons and top affiliate deals on ToolzyNet.');

-- ---- Brands ----
INSERT INTO brands (name, slug, is_active) VALUES
('Apple','apple',1),('Samsung','samsung',1),('Sony','sony',1),('boAt','boat',1),
('OnePlus','oneplus',1),('HP','hp',1),('Dell','dell',1),('Nike','nike',1),
('Puma','puma',1),('Realme','realme',1);

-- ---- Settings ----
INSERT INTO settings (`key`, `value`, `group`) VALUES
('site_name','ToolzyNet','general'),
('tagline','Compare. Review. Buy Smart.','general'),
('contact_email','hello@toolzynet.com','general'),
('footer_copyright','© 2026 ToolzyNet.com — All rights reserved.','general'),
('theme_color','#2563eb','appearance'),
('dark_mode','0','appearance'),
('maintenance_mode','0','general'),
('meta_title','ToolzyNet — Product Reviews, Comparisons & Best Deals','seo'),
('meta_description','ToolzyNet helps you compare products, read honest reviews and find the best affiliate deals from Amazon, Flipkart, Meesho and more.','seo'),
('social_facebook','','social'),
('social_twitter','','social'),
('social_instagram','','social'),
('social_youtube','','social')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- ---- Sample product (Apple iPhone 16 Pro) ----
INSERT INTO products
 (category_id, brand_id, name, slug, short_description, long_description, features, specifications, pros, cons, faq,
  rating, rating_count, price, original_price, discount, amazon_link, flipkart_link, status, published_at,
  is_featured, is_trending, is_deal, is_bestseller, is_new, meta_title, meta_description)
SELECT
  (SELECT id FROM categories WHERE slug='mobiles' LIMIT 1),
  (SELECT id FROM brands WHERE slug='apple' LIMIT 1),
  'Apple iPhone 16 Pro','apple-iphone-16-pro',
  'The most advanced iPhone with A18 Pro chip and titanium design.',
  '<p>The iPhone 16 Pro pushes performance and camera quality to new heights with a titanium frame, the A18 Pro chip, and an advanced 48MP camera system.</p>',
  '["A18 Pro chip","48MP Fusion camera","Titanium body","USB-C","120Hz ProMotion"]',
  '{"Display":"6.3\\" OLED 120Hz","Chip":"A18 Pro","RAM":"8GB","Storage":"256GB","Battery":"3600mAh"}',
  '["Excellent cameras","Premium build","Fast performance"]',
  '["Expensive","No charger in box"]',
  '[{"q":"Does it support 5G?","a":"Yes, the iPhone 16 Pro supports 5G networks."},{"q":"Is it waterproof?","a":"It has IP68 water and dust resistance."}]',
  4.7, 128, 119900, 134900, 11,
  'https://www.amazon.in/', 'https://www.flipkart.com/', 'published', NOW(),
  1,1,1,1,1,
  'Apple iPhone 16 Pro Review, Price & Best Deals | ToolzyNet',
  'Full review, specs, pros & cons and the best price for the Apple iPhone 16 Pro on ToolzyNet.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug='apple-iphone-16-pro');

INSERT INTO product_images (product_id, path, alt, is_primary, sort_order)
SELECT id, 'products/placeholder-phone.png', 'Apple iPhone 16 Pro', 1, 0
FROM products WHERE slug='apple-iphone-16-pro'
AND NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id = products.id);
