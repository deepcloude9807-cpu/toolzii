<?php
/**
 * Route definitions. $router is provided by App::registerRoutes().
 * SEO-friendly URLs, no query-string ids on public pages.
 *
 * @var \App\Core\Router $router
 */

/* ---------------- Public ---------------- */
$router->get('/', 'HomeController@index');

// Search (AJAX + full page)
$router->get('/search', 'SearchController@index');
$router->get('/api/search', 'SearchController@suggest');       // autocomplete JSON
$router->get('/api/products/filter', 'SearchController@filter'); // faceted filter JSON

// Newsletter + contact + click tracking (AJAX POST)
$router->post('/newsletter/subscribe', 'PageController@subscribe');
$router->match(['GET', 'POST'], '/contact', 'PageController@contact');
$router->get('/go/{id}', 'ProductController@track');           // affiliate redirect + click counter

// Blog
$router->get('/blog', 'BlogController@index');
$router->get('/blog/{slug}', 'BlogController@show');
$router->post('/blog/{slug}/comment', 'BlogController@comment');

// Comparison
$router->get('/compare', 'ComparisonController@index');
$router->get('/compare/{slug}', 'ComparisonController@show');

// Static pages
$router->get('/about', 'PageController@about');
$router->get('/privacy-policy', 'PageController@privacy');
$router->get('/disclosure', 'PageController@disclosure');
$router->get('/terms', 'PageController@terms');

// SEO endpoints
$router->get('/sitemap.xml', 'SitemapController@index');
$router->get('/robots.txt', 'SitemapController@robots');

// Category + product (keep last: greedy slugs)
$router->get('/category/{slug}', 'CategoryController@show');
$router->post('/product/{slug}/review', 'ProductController@review');
$router->get('/product/{slug}', 'ProductController@show');

/* ---------------- Admin ---------------- */
$router->match(['GET', 'POST'], '/admin/login', 'Admin\AuthController@login');
$router->post('/admin/logout', 'Admin\AuthController@logout');
$router->match(['GET', 'POST'], '/admin/forgot-password', 'Admin\AuthController@forgot');
$router->match(['GET', 'POST'], '/admin/reset-password/{token}', 'Admin\AuthController@reset');

$router->get('/admin', 'Admin\DashboardController@index');
$router->get('/admin/dashboard', 'Admin\DashboardController@index');
$router->get('/admin/analytics', 'Admin\DashboardController@analytics');

// Products
$router->get('/admin/products', 'Admin\ProductController@index');
$router->match(['GET', 'POST'], '/admin/products/create', 'Admin\ProductController@create');
$router->match(['GET', 'POST'], '/admin/products/{id}/edit', 'Admin\ProductController@edit');
$router->post('/admin/products/{id}/delete', 'Admin\ProductController@destroy');
$router->match(['GET', 'POST'], '/admin/products/import', 'Admin\ProductController@import');

// Categories
$router->get('/admin/categories', 'Admin\CategoryController@index');
$router->match(['GET', 'POST'], '/admin/categories/create', 'Admin\CategoryController@create');
$router->match(['GET', 'POST'], '/admin/categories/{id}/edit', 'Admin\CategoryController@edit');
$router->post('/admin/categories/{id}/delete', 'Admin\CategoryController@destroy');

// Brands
$router->get('/admin/brands', 'Admin\BrandController@index');
$router->match(['GET', 'POST'], '/admin/brands/create', 'Admin\BrandController@create');
$router->match(['GET', 'POST'], '/admin/brands/{id}/edit', 'Admin\BrandController@edit');
$router->post('/admin/brands/{id}/delete', 'Admin\BrandController@destroy');

// Blog
$router->get('/admin/blog', 'Admin\BlogController@index');
$router->match(['GET', 'POST'], '/admin/blog/create', 'Admin\BlogController@create');
$router->match(['GET', 'POST'], '/admin/blog/{id}/edit', 'Admin\BlogController@edit');
$router->post('/admin/blog/{id}/delete', 'Admin\BlogController@destroy');

// Settings + SEO
$router->match(['GET', 'POST'], '/admin/settings', 'Admin\SettingController@index');
