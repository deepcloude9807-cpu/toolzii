# ToolzyNet — FTP se Hostinger par Upload (Step-by-Step)

FTP se code apne computer se Hostinger par upload hota hai. Isliye pehle
repo ka code apne PC par laayein, phir FileZilla se upload karein.

---

## Step 0 — Code apne computer par laayein
1. GitHub par repo kholein → **Code (green button) → Download ZIP**
   (branch: `claude/new-session-dk8693`)
2. ZIP ko apne PC par **extract** kar lein. Ab aapke paas ye folders honge:
   `app, config, database, public, storage, .env.hostinger, README.md` ...

---

## Step 1 — FTP details lein (Hostinger hPanel)
hPanel → **Files → FTP Accounts**. Yahan milega:
- **FTP Host / IP:** jaise `ftp.yourdomain.com` ya ek IP address
- **FTP Username:** jaise `u123456.yourdomain.com`
- **FTP Password:** (aap set/reset kar sakte hain)
- **Port:** `21`

---

## Step 2 — FileZilla install & connect
1. **FileZilla** download karein (free): https://filezilla-project.org
2. Upar ke boxes me daalein:
   - Host: `ftp://ftp.yourdomain.com`
   - Username / Password: upar wale
   - Port: `21`  → **Quickconnect** dabayein
3. **ZAROORI:** hidden files (jaise `.env`, `.htaccess`) dikhane ke liye:
   FileZilla menu → **Server → Force showing hidden files** ON kar dein.

Connect hone par: LEFT side = aapka computer, RIGHT side = Hostinger server.

---

## Step 3 — Files sahi jagah upload karein  (SABSE ZAROORI)

Server (right side) par aapko `public_html` folder dikhega. Structure aisa
banana hai:

```
(home / root — public_html ke bahar)
   ├── app/            ← upload karein
   ├── config/         ← upload karein
   ├── database/       ← upload karein
   ├── storage/        ← upload karein
   ├── .env            ← (.env.hostinger ko rename karke, niche Step 4)
   └── public_html/    ← iske ANDAR public/ ki cheezein daalein:
        ├── index.php
        ├── .htaccess
        ├── assets/
        └── uploads/
```

Yani:
- Apne PC ke `public/` folder ke **ANDAR ki saari files** → server ke
  **`public_html/`** me drag karein.
- `app`, `config`, `database`, `storage` folders → `public_html` ke
  **bahar** (usi jagah jahan public_html hai) drag karein.

> Agar aapka FTP sirf `public_html` ke andar hi le jaata hai (bahar jaane
> nahi deta), to niche "Alternative" dekhein.

---

## Step 4 — .env file banayein
1. Apne PC par `.env.hostinger` file ko text editor me kholein.
2. In 4 lines ko apne hisaab se bharein: `APP_URL`, `DB_NAME`, `DB_USER`,
   `DB_PASS`. (APP_KEY pehle se bhara hai.)
3. File ka naam badal kar **`.env`** kar dein.
4. Ise server par `public_html` ke **bahar** (app/ ke saath) upload karein.

---

## Step 5 — Permissions (writable folders)
FileZilla me right-click → **File permissions** → `755` set karein:
- `storage/`  (aur andar `cache`, `logs`)
- `public_html/uploads/`

---

## Step 6 — Database import (agar abhi nahi kiya)
hPanel → **phpMyAdmin** → apna DB → **Import**:
1. pehle `database/schema.sql`  → Go
2. phir `database/seed.sql`      → Go

---

## Step 7 — Test
- Website: `https://yourdomain.com`
- Admin:   `https://yourdomain.com/admin/login`
  (login: `admin@toolzynet.com` / `Admin@12345` — turant password badlein)

---

## Alternative — agar FTP sirf public_html tak hi jaata hai
Sab kuch `public_html` ke andar daal dein (poora project), phir:
hPanel → **Domains → domain → Document Root** ko `public_html/public`
kar dein. Isse URL saaf rahega aur app/config/.env web se surakshit rahenge.

---

## Aam dikkatein
| Dikkat | Hal |
|--------|-----|
| `.env` / `.htaccess` dikh nahi rahi | FileZilla → Server → Force showing hidden files ON karein |
| 500 / white page | `.env` galat jagah ya DB details galat. APP_DEBUG=true karke error dekhein, phir false |
| CSS nahi aa rahi | `.htaccess` public_html me hai? APP_URL sahi domain hai? |
| Images upload nahi hoti | `public_html/uploads` ko 755 karein |
