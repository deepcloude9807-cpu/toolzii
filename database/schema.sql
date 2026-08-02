-- =====================================================================
-- ToolzyNet.com — Affiliate Marketing Platform
-- MySQL 8+ schema (InnoDB, utf8mb4). Normalized, FK-enforced, indexed,
-- with soft-delete support on core content tables.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------
-- Users / roles
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('admin','editor','author') NOT NULL DEFAULT 'author',
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    email_verified_at DATETIME NULL,
    remember_token  VARCHAR(100) NULL,
    reset_token     VARCHAR(100) NULL,
    reset_expires_at DATETIME NULL,
    last_login_at   DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Categories (self-referencing parent for nesting)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id       BIGINT UNSIGNED NULL,
    name            VARCHAR(150) NOT NULL,
    slug            VARCHAR(170) NOT NULL,
    description     TEXT NULL,
    image           VARCHAR(255) NULL,
    icon            VARCHAR(60) NULL,
    meta_title      VARCHAR(190) NULL,
    meta_description VARCHAR(300) NULL,
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_parent (parent_id),
    KEY idx_categories_active (is_active),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id)
        REFERENCES categories (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Brands
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS brands (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(150) NOT NULL,
    slug            VARCHAR(170) NOT NULL,
    logo            VARCHAR(255) NULL,
    description     TEXT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_brands_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Products
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id     BIGINT UNSIGNED NULL,
    brand_id        BIGINT UNSIGNED NULL,
    name            VARCHAR(200) NOT NULL,
    slug            VARCHAR(220) NOT NULL,
    short_description VARCHAR(500) NULL,
    long_description MEDIUMTEXT NULL,
    features        JSON NULL,               -- array of strings
    specifications  JSON NULL,               -- object {label: value}
    pros            JSON NULL,               -- array of strings
    cons            JSON NULL,               -- array of strings
    faq             JSON NULL,               -- array of {q, a}
    rating          DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    rating_count    INT UNSIGNED NOT NULL DEFAULT 0,
    price           DECIMAL(12,2) NULL,
    original_price  DECIMAL(12,2) NULL,
    discount        INT NULL,                -- percentage (derived/override)
    amazon_link     VARCHAR(500) NULL,
    flipkart_link   VARCHAR(500) NULL,
    meesho_link     VARCHAR(500) NULL,
    custom_link     VARCHAR(500) NULL,
    views           INT UNSIGNED NOT NULL DEFAULT 0,
    is_featured     TINYINT(1) NOT NULL DEFAULT 0,
    is_trending     TINYINT(1) NOT NULL DEFAULT 0,
    is_deal         TINYINT(1) NOT NULL DEFAULT 0,
    is_bestseller   TINYINT(1) NOT NULL DEFAULT 0,
    is_new          TINYINT(1) NOT NULL DEFAULT 0,
    status          ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
    published_at    DATETIME NULL,
    meta_title      VARCHAR(190) NULL,
    meta_description VARCHAR(300) NULL,
    og_image        VARCHAR(255) NULL,
    created_by      BIGINT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_slug (slug),
    KEY idx_products_category (category_id),
    KEY idx_products_brand (brand_id),
    KEY idx_products_status (status),
    KEY idx_products_flags (is_featured, is_trending, is_deal),
    KEY idx_products_price (price),
    FULLTEXT KEY ft_products (name, short_description, long_description),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_products_brand FOREIGN KEY (brand_id)
        REFERENCES brands (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_products_user FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Product images (gallery, one primary)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS product_images (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id      BIGINT UNSIGNED NOT NULL,
    path            VARCHAR(255) NOT NULL,
    alt             VARCHAR(190) NULL,
    is_primary      TINYINT(1) NOT NULL DEFAULT 0,
    sort_order      INT NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_pimg_product (product_id),
    CONSTRAINT fk_pimg_product FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Affiliate click tracking
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS affiliate_clicks (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id      BIGINT UNSIGNED NOT NULL,
    partner         ENUM('amazon','flipkart','meesho','custom') NOT NULL,
    ip_hash         CHAR(64) NULL,
    user_agent      VARCHAR(255) NULL,
    referer         VARCHAR(500) NULL,
    clicked_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_clicks_product (product_id),
    KEY idx_clicks_partner (partner),
    KEY idx_clicks_date (clicked_at),
    CONSTRAINT fk_clicks_product FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Product reviews (visitor star ratings)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id      BIGINT UNSIGNED NOT NULL,
    author_name     VARCHAR(120) NOT NULL,
    author_email    VARCHAR(190) NULL,
    rating          TINYINT UNSIGNED NOT NULL DEFAULT 5,
    title           VARCHAR(190) NULL,
    body            TEXT NULL,
    is_approved     TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reviews_product (product_id),
    KEY idx_reviews_approved (is_approved),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Comparison articles (iPhone vs Samsung, etc.)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comparisons (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title           VARCHAR(220) NOT NULL,
    slug            VARCHAR(240) NOT NULL,
    intro           TEXT NULL,
    product_a_id    BIGINT UNSIGNED NULL,
    product_b_id    BIGINT UNSIGNED NULL,
    winner_id       BIGINT UNSIGNED NULL,
    verdict         TEXT NULL,
    comparison_table JSON NULL,              -- [{feature, a, b}]
    status          ENUM('draft','published') NOT NULL DEFAULT 'draft',
    meta_title      VARCHAR(190) NULL,
    meta_description VARCHAR(300) NULL,
    views           INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_comparisons_slug (slug),
    KEY idx_comparisons_status (status),
    CONSTRAINT fk_cmp_a FOREIGN KEY (product_a_id) REFERENCES products (id) ON DELETE SET NULL,
    CONSTRAINT fk_cmp_b FOREIGN KEY (product_b_id) REFERENCES products (id) ON DELETE SET NULL,
    CONSTRAINT fk_cmp_winner FOREIGN KEY (winner_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Blog
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS blog_posts (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id     BIGINT UNSIGNED NULL,
    author_id       BIGINT UNSIGNED NULL,
    title           VARCHAR(220) NOT NULL,
    slug            VARCHAR(240) NOT NULL,
    excerpt         VARCHAR(500) NULL,
    body            LONGTEXT NULL,
    featured_image  VARCHAR(255) NULL,
    tags            JSON NULL,
    views           INT UNSIGNED NOT NULL DEFAULT 0,
    reading_minutes INT NOT NULL DEFAULT 1,
    status          ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
    published_at    DATETIME NULL,
    meta_title      VARCHAR(190) NULL,
    meta_description VARCHAR(300) NULL,
    og_image        VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_blog_slug (slug),
    KEY idx_blog_status (status),
    KEY idx_blog_category (category_id),
    FULLTEXT KEY ft_blog (title, excerpt, body),
    CONSTRAINT fk_blog_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_comments (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id         BIGINT UNSIGNED NOT NULL,
    author_name     VARCHAR(120) NOT NULL,
    author_email    VARCHAR(190) NULL,
    body            TEXT NOT NULL,
    is_approved     TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_post (post_id),
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES blog_posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Banners
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS banners (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title           VARCHAR(190) NULL,
    image           VARCHAR(255) NOT NULL,
    link            VARCHAR(500) NULL,
    position        ENUM('homepage','sidebar','popup','deal') NOT NULL DEFAULT 'homepage',
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    starts_at       DATETIME NULL,
    ends_at         DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_banners_position (position, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Newsletter subscribers
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscribers (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email           VARCHAR(190) NOT NULL,
    is_confirmed    TINYINT(1) NOT NULL DEFAULT 0,
    token           VARCHAR(64) NULL,
    ip_hash         CHAR(64) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_subscribers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Contact messages
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    subject         VARCHAR(190) NULL,
    message         TEXT NOT NULL,
    ip_hash         CHAR(64) NULL,
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contact_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Search log (analytics)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS search_logs (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    term            VARCHAR(190) NOT NULL,
    results_count   INT NOT NULL DEFAULT 0,
    ip_hash         CHAR(64) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_search_term (term)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------
-- Settings (key/value)
-- ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`           VARCHAR(100) NOT NULL,
    `value`         TEXT NULL,
    `group`         VARCHAR(60) NOT NULL DEFAULT 'general',
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_settings_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
