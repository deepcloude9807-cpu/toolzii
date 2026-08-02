<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\AffiliateClick;

final class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin', 'editor', 'author');
    }

    public function index(): string
    {
        $clicks = new AffiliateClick();
        $stats = [
            'products'    => (int) Database::value('SELECT COUNT(*) FROM products WHERE deleted_at IS NULL'),
            'categories'  => (int) Database::value('SELECT COUNT(*) FROM categories WHERE deleted_at IS NULL'),
            'posts'       => (int) Database::value('SELECT COUNT(*) FROM blog_posts WHERE deleted_at IS NULL'),
            'clicks'      => $clicks->total(),
            'subscribers' => (int) Database::value('SELECT COUNT(*) FROM subscribers'),
            'messages'    => (int) Database::value('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0'),
        ];

        return $this->view('admin/dashboard/index', [
            'stats'         => $stats,
            'topProducts'   => Database::all('SELECT name, slug, views FROM products WHERE deleted_at IS NULL ORDER BY views DESC LIMIT 5'),
            'topClicked'    => $clicks->topProducts(5),
            'recentReviews' => Database::all('SELECT r.author_name, r.rating, r.created_at, p.name product FROM reviews r JOIN products p ON p.id = r.product_id ORDER BY r.created_at DESC LIMIT 6'),
            'clicksSeries'  => $clicks->last30Days(),
        ], 'admin/layouts/app');
    }

    public function analytics(): string
    {
        $clicks = new AffiliateClick();
        return $this->view('admin/dashboard/analytics', [
            'byPartner'    => $clicks->byPartner(),
            'clicksSeries' => $clicks->last30Days(),
            'topClicked'   => $clicks->topProducts(10),
            'topSearches'  => Database::all('SELECT term, COUNT(*) c FROM search_logs GROUP BY term ORDER BY c DESC LIMIT 15'),
            'mostViewed'   => Database::all('SELECT name, slug, views FROM products WHERE deleted_at IS NULL ORDER BY views DESC LIMIT 10'),
        ], 'admin/layouts/app');
    }
}
