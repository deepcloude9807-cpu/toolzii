<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class AffiliateClick extends Model
{
    protected string $table = 'affiliate_clicks';

    public function record(int $productId, string $partner, string $ipHash, string $ua, ?string $referer): void
    {
        Database::run(
            'INSERT INTO affiliate_clicks (product_id, partner, ip_hash, user_agent, referer)
             VALUES (?, ?, ?, ?, ?)',
            [$productId, $partner, $ipHash, $ua, $referer]
        );
    }

    public function total(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM affiliate_clicks');
    }

    public function byPartner(): array
    {
        return Database::all(
            'SELECT partner, COUNT(*) c FROM affiliate_clicks GROUP BY partner ORDER BY c DESC'
        );
    }

    public function last30Days(): array
    {
        return Database::all(
            'SELECT DATE(clicked_at) d, COUNT(*) c
             FROM affiliate_clicks
             WHERE clicked_at >= (CURRENT_DATE - INTERVAL 29 DAY)
             GROUP BY DATE(clicked_at) ORDER BY d ASC'
        );
    }

    public function topProducts(int $limit = 5): array
    {
        return Database::all(
            'SELECT p.name, p.slug, COUNT(a.id) clicks
             FROM affiliate_clicks a
             JOIN products p ON p.id = a.product_id
             GROUP BY a.product_id ORDER BY clicks DESC LIMIT ?',
            [$limit]
        );
    }
}
