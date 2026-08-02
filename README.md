# ToolzyNet.com — Affiliate Marketing Platform

A modern, SEO-optimized affiliate marketing website built with **PHP 8+ (custom MVC)**, **MySQL 8**, **Bootstrap 5**, and **vanilla JS + AJAX**. Visitors browse products, compare them, read reviews, and click affiliate links to Amazon, Flipkart, Meesho and custom partners. No cart, checkout, or payment gateway.

> Built with clean MVC, PDO prepared statements, CSRF/XSS protection, SEO-friendly URLs, schema.org markup, and a professional admin dashboard.

---

## ✨ Features

**Frontend**
- Homepage: hero, AJAX search, trending, today's deals, top categories, featured reviews, comparison articles, latest blog, newsletter, footer
- Product pages: gallery, brand, features, specifications, pros/cons, rating, price/discount, multi-partner buy buttons, FAQ (accordion), reviews, related products, social sharing, sticky sidebar
- Comparison pages: side-by-side table, winner, pros/cons, affiliate buttons, verdict
- Blog: categories, tags, featured image, reading time, related posts, comments
- AJAX search with autocomplete + faceted filters (category, brand, price, sort)
- Newsletter (AJAX), contact form, breadcrumbs, 404, maintenance mode, dark mode

**SEO**
- Per-page meta title/description, canonical, Open Graph, Twitter cards
- JSON-LD schema: Product, FAQPage, BreadcrumbList, BlogPosting, WebSite+SearchAction
- Dynamic `sitemap.xml` and `robots.txt`

**Admin panel** (`/admin`)
- Secure login, forgot/reset password, roles (admin/editor/author)
- Dashboard: counts, click chart (30 days), top/most-viewed products, recent reviews
- Products: full CRUD, gallery upload, JSON features/specs/pros/cons/FAQ, flags (featured/trending/deal/bestseller/new), status (draft/published/scheduled), CSV bulk import
- Categories (nested), Brands, Blog — all CRUD
- Affiliate click tracking + analytics (by partner, top searches)
- Settings: site name, logo, favicon, SMTP, socials, theme color, dark mode, maintenance, SEO defaults

**Security & performance**
- CSRF tokens on all mutating requests, PDO prepared statements everywhere
- `password_hash`/`password_verify` with auto-rehash, session hardening & regeneration
- Real-MIME image validation on uploads, file-based rate limiting on forms/login
- Output escaping helper `e()`, security headers via `.htaccess`
- Lazy-loaded images, gzip + cache headers, pagination, indexed & normalized schema with FKs and soft deletes

---

## 🧱 Architecture

```
public/            Web root (point your vhost here)
  index.php        Front controller
  .htaccess        Rewrites, security headers, caching
  assets/          css / js / img
  uploads/         User uploads (products, brands, blog, banners)
app/
  Core/            Framework: App, Router, Controller, Model, View,
                   Database (PDO), Auth, Session, Csrf, Validator,
                   Upload, RateLimiter, Seo, Env, Helpers
  Controllers/     Public + Admin/ controllers
  Models/          Product, Category, Brand, Blog, Comparison, User,
                   Setting, Review, AffiliateClick, Subscriber, Banner
  Views/           layouts / partials / pages + admin/
config/            app.php, routes.php
database/          schema.sql, seed.php
storage/           cache (+rate limits), logs
```

Request flow: `public/index.php` → `bootstrap.php` (PSR-4 autoload, env, error handling) → `App` (session, shared view data, route dispatch, CSRF verify) → `Controller` → `Model` (PDO) → `View` (layout + template).

---

## 🚀 Installation

**Requirements:** PHP 8.1+ (`pdo_mysql`, `mbstring`, `fileinfo`), MySQL 8 / MariaDB 10.4+, Apache with `mod_rewrite` (or Nginx equivalent).

1. **Clone & configure**
   ```bash
   cp .env.example .env
   # edit .env: set DB_*, APP_URL, APP_KEY, mail, etc.
   ```

2. **Create the database & import schema**
   ```bash
   mysql -u root -p -e "CREATE DATABASE toolzynet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p toolzynet < database/schema.sql
   ```

3. **Seed defaults** (admin user, 19 categories, brands, settings, sample product)
   ```bash
   php database/seed.php
   ```
   Default admin: **admin@toolzynet.com** / **Admin@12345** — change immediately after first login.

4. **Permissions**
   ```bash
   chmod -R 775 storage public/uploads
   ```

5. **Run**
   - Production: point your Apache/Nginx document root to `public/`.
   - Local dev:
     ```bash
     php -S localhost:8080 -t public
     ```
   Visit `http://localhost:8080` and the admin at `http://localhost:8080/admin/login`.

> No Composer packages are required — a lightweight PSR-4 autoloader is built in. `composer.json` is provided so you can add packages (e.g. PHPMailer) later.

### Nginx (equivalent to the bundled `.htaccess`)
```nginx
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.1-fpm.sock; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; }
```

---

## 🔌 Key routes

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/` | Homepage |
| GET | `/search`, `/api/search`, `/api/products/filter` | Search page, autocomplete, AJAX filter |
| GET | `/category/{slug}` | Category listing + filters |
| GET | `/product/{slug}` | Product detail |
| GET | `/go/{id}?partner=amazon` | Affiliate redirect + click tracking |
| GET | `/compare`, `/compare/{slug}` | Comparisons |
| GET | `/blog`, `/blog/{slug}` | Blog |
| GET | `/sitemap.xml`, `/robots.txt` | SEO |
| — | `/admin/...` | Admin dashboard & CRUD |

---

## 🗄️ CSV product import

Upload at **Admin → Products → Import CSV**. Header row columns:

```
name, category, brand, price, original_price, amazon_link, short_description, status
```
`category`/`brand` match by slug or name; `status` = `draft` or `published`.

---

## 🔐 Security notes for production
- Set `APP_DEBUG=false` and a strong random `APP_KEY` in `.env`.
- Serve over HTTPS (uncomment the redirect block in `public/.htaccess`).
- Keep `.env`, `storage/`, `database/` outside the web root or blocked (the `public/` root already isolates them).
- Configure real SMTP credentials in `.env` for password-reset emails (the reset link is currently written to the error log for dev).

---

## 🧭 Scope & extension notes
This repository is a production-ready **foundation** implementing the full public site, the core admin CRUD (products, categories, brands, blog, settings), affiliate tracking, analytics, SEO, and security. Areas intentionally left as clean extension points:
- **Comparison & Banner admin CRUD** — models and public views exist; add admin controllers mirroring `Admin/BrandController` (routes are easy to append in `config/routes.php`).
- **Rich-text editor** — the blog/body textareas are ready for TinyMCE/CKEditor drop-in.
- **Email delivery** — wire PHPMailer into a `Mailer` core class using the `.env` SMTP settings.
- **Wishlist / recently-viewed** — client-side hooks; persist via `localStorage` or a `wishlists` table.

## License
MIT
