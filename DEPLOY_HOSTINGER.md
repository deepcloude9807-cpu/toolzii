# ToolzyNet — Hostinger पर Live कैसे करें (Step-by-Step)

यह project MVC structure में है: web-root `public/` folder है और बाकी code (`app/`, `config/` आदि) उसके **बाहर** रहता है (security के लिए)। नीचे दिया तरीका इसी हिसाब से है।

> जो चाहिए: एक Hostinger plan (कोई भी), आपका domain, और hPanel access.

---

## 🗂️ Step 1 — सही Folder Structure समझें

Hostinger पर आपका home folder ऐसा दिखता है:
```
/home/uXXXXXXXX/
   ├── public_html/      ← यहाँ जो होगा वही domain पर दिखेगा
   └── (बाकी सब यहाँ रख सकते हैं, ये web पर नहीं दिखता — सुरक्षित)
```

हम code को ऐसे रखेंगे:
```
/home/uXXXXXXXX/
   ├── app/              ← project का app/ folder
   ├── config/           ← project का config/ folder
   ├── database/         ← project का database/ folder
   ├── storage/          ← project का storage/ folder
   ├── .env              ← production settings (नीचे बनाएंगे)
   └── public_html/      ← project के public/ folder के **अंदर की चीज़ें**
        ├── index.php
        ├── .htaccess
        ├── assets/
        └── uploads/
```

⚠️ ध्यान: `public/` folder खुद upload मत करो — उसके **अंदर की files** `public_html/` में जाएँगी।

---

## 📤 Step 2 — Files Upload करें

**तरीका A — GitHub से (सबसे आसान अगर SSH हो):**
Hostinger के **hPanel → Advanced → SSH Access** से:
```bash
cd ~
git clone <आपका-repo-url> toolzynet
mv toolzynet/public_html_backup 2>/dev/null   # (अगर पुराना हो)
# app/config/database/storage को home में लाएँ
mv toolzynet/app toolzynet/config toolzynet/database toolzynet/storage ~/
# public/ की files public_html में
cp -r toolzynet/public/. ~/public_html/
```

**तरीका B — File Manager / ZIP से (बिना SSH):**
1. GitHub से project का ZIP download करें।
2. hPanel → **File Manager** खोलें।
3. `app`, `config`, `database`, `storage` folders को home directory (`/home/uXXXX/`) में upload करें।
4. project के `public/` folder के अंदर की सारी files (`index.php`, `.htaccess`, `assets`, `uploads`) को **`public_html/`** में upload करें।

---

## 🗄️ Step 3 — MySQL Database बनाएं

1. hPanel → **Databases → MySQL Databases**
2. नया database बनाएं — Hostinger नाम में prefix लगाता है, जैसे: `u123456_toolzynet`
3. नया user बनाएं और एक strong password रखें, फिर उस user को database से **All Privileges** के साथ जोड़ें।
4. ये तीन चीज़ें note कर लें:
   - Database name: `u123456_toolzynet`
   - Username: `u123456_toolzy`
   - Password: `********`

---

## 📥 Step 4 — Tables + Data Import करें (phpMyAdmin)

1. hPanel → **Databases → phpMyAdmin** → अपना database खोलें।
2. **Import** tab → `database/schema.sql` file चुनें → **Go**. (सारी tables बन जाएँगी)
3. फिर से **Import** tab → `database/seed.sql` file चुनें → **Go**.
   (इससे admin account + 19 categories + brands + settings + 1 sample product आ जाएगा — कोई command नहीं चलानी पड़ेगी ✅)

**Admin login:** `admin@toolzynet.com` / `Admin@12345` — login के तुरंत बाद password बदल दें।

---

## ⚙️ Step 5 — `.env` file बनाएं

File Manager में home directory (`/home/uXXXX/`, `public_html` के बाहर) में एक नई file `.env` बनाएं और ये डालें (अपनी details भरकर):

```env
APP_NAME="ToolzyNet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Kolkata

DB_HOST=localhost
DB_PORT=3306
DB_NAME=u123456_toolzynet
DB_USER=u123456_toolzy
DB_PASS=आपका_db_password
DB_CHARSET=utf8mb4

APP_KEY=कोई-random-32-अक्षर-यहाँ-डालें
SESSION_NAME=toolzynet_session
SESSION_LIFETIME=7200

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=you@yourdomain.com
MAIL_PASSWORD=email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="ToolzyNet"

UPLOAD_MAX_SIZE=3145728
```

> Hostinger पर `DB_HOST` आमतौर पर `localhost` होता है (127.0.0.1 नहीं)।

---

## 🔐 Step 6 — Permissions सेट करें

File Manager में इन दो folders को writable बनाएं (Right-click → Permissions → 755):
- `storage/` (और उसके अंदर `cache`, `logs`)
- `public_html/uploads/`

---

## 🌐 Step 7 — PHP Version + Test

1. hPanel → **Advanced → PHP Configuration** → PHP version **8.1 या ऊपर** चुनें, और `pdo_mysql`, `mbstring`, `fileinfo` extensions on रखें।
2. अपना domain खोलें: `https://yourdomain.com` — homepage दिखना चाहिए ✅
3. Admin: `https://yourdomain.com/admin/login`

---

## 🧩 अगर पूरा project `public_html` के बाहर नहीं रख पा रहे (Alternative)

कुछ basic setups में home folder access मुश्किल लगता है। तो पूरा project `public_html/` के अंदर डाल दें और domain को `public_html/public` पर point करें:
- hPanel → **Domains → domain के आगे "Manage" → Document Root** को `public_html/public` कर दें।
- इससे `app/`, `config/`, `.env` अपने आप web से बाहर/सुरक्षित रहेंगे और URL भी साफ़ रहेगा।

---

## ❗ आम दिक्कतें
| दिक्कत | हल |
|--------|----|
| सफ़ेद/500 page | `.env` में DB details गलत, या `.env` गलत जगह है। `APP_DEBUG=true` करके error देखें, फिर वापस false कर दें। |
| CSS/design नहीं आ रहा | `.htaccess` `public_html` में है या नहीं देखें; `APP_URL` सही domain हो। |
| Images upload नहीं हो रहीं | `public_html/uploads` की permission 755 करें। |
| Category/product page 404 | `mod_rewrite` on है (Hostinger पर default on) और `.htaccess` मौजूद है — confirm करें। |

काम करते वक्त कोई error दिखे तो वो message यहाँ paste कर देना, मैं fix कर दूँगा। 🚀
