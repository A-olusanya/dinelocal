# DineLocal

A full-stack restaurant management web application built with PHP, MySQL, and Bootstrap 5. DineLocal allows customers to browse the menu, make reservations, and manage their accounts — while giving restaurant staff a dedicated admin panel to manage everything.

**Live site:** [https://dinelocal-f4hehddpe6bmhnh0.canadacentral-01.azurewebsites.net/welcome.php](https://dinelocal.azurewebsites.net)

---

## Features

### Customer-facing
- Welcome landing page with animated entrance
- User registration and login with "Remember me"
- Forgot password / forced password change flow
- Browse full restaurant menu
- Make, view, and manage reservations
- Personal dashboard

### Admin panel (`/admin`)
- Secure staff login (separate from customer accounts)
- Dashboard with live stats: total reservations, pending, confirmed, menu items
- Manage reservations (confirm / cancel)
- Manage menu items (add, edit, delete)
- Manage users and admins
- Role-based access control (`admin` / `super_admin`)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2 |
| Database | MySQL (hosted on Railway) |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Cormorant Garamond + Inter fonts |
| Hosting | Azure App Service Linux (Basic B1) |
| Deployment | GitHub Actions (push to `main` → auto-deploy) |
| Web server | Apache with `.htaccess` |

---

## Project Structure

```
dinelocal/
├── admin/                  # Admin panel pages
│   ├── index.php           # Dashboard
│   ├── manage-reservations.php
│   ├── manage-menu.php
│   ├── manage-users.php
│   ├── manage-admins.php
│   ├── login.php
│   └── logout.php
├── config/
│   └── db.php              # PDO database connection (env-var based)
├── controllers/
│   ├── AdminController.php
│   ├── MenuController.php
│   └── ReservationController.php
├── models/
│   ├── User.php
│   ├── Reservation.php
│   ├── Menu.php
│   └── Database.php
├── views/partials/
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── assets/                 # CSS, JS, images
├── index.php               # Homepage (redirects to welcome on first visit)
├── welcome.php             # Animated welcome/landing page
├── login.php               # Customer login
├── register.php            # Customer registration
├── dashboard.php           # Customer dashboard
├── menu.php                # Menu page
├── reservations.php        # Reservations page
├── about.php               # About page
├── .htaccess               # Apache: caching, compression, security, routing
├── .user.ini               # PHP OPcache + session tuning for Azure
└── startup.sh              # Azure startup script (creates /tmp dirs for OPcache)
```

---

## Local Development

### Requirements
- PHP 8.2+
- MySQL 8+
- Apache with `mod_rewrite` enabled

### Setup

1. Clone the repo:
   ```bash
   git clone https://github.com/A-olusanya/dinelocal.git
   cd dinelocal
   ```

2. Set up the database — import the schema into a local MySQL instance:
   ```bash
   mysql -u root -p your_database < railway_import.sql
   ```

3. Set environment variables (or create a local `.env` loader):
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=your_database
   DB_USER=root
   DB_PASS=your_password
   ```

4. Point your web server document root at the project folder and visit `http://localhost`.

---

## Deployment (Azure App Service)

Deployments are fully automated via GitHub Actions (`.github/workflows/main_dinelocal.yml`).

Every push to `main` triggers:
1. PHP app zip upload to Azure Web App `dinelocal`
2. Azure runs `startup.sh` which creates `/tmp/opcache` and `/tmp/php_sessions`
3. PHP-FPM picks up `.user.ini` (OPcache + session config)

### Required Azure App Settings (Environment Variables)

| Name | Description |
|---|---|
| `DB_HOST` | Railway MySQL proxy host |
| `DB_PORT` | Railway MySQL proxy port |
| `DB_NAME` | Database name |
| `DB_USER` | Database username |
| `DB_PASS` | Database password |
| `SCM_DO_BUILD_DURING_DEPLOYMENT` | Set to `false` |

### Azure Configuration
- **Stack:** PHP 8.2
- **Always On:** Enabled
- **HTTPS Only:** Enabled
- **Startup Command:** `/home/site/wwwroot/startup.sh`

---

## Performance Optimizations

- **OPcache** with `file_cache=/tmp` — eliminates SMB stat() calls on every request (30–50% PHP speedup on Azure)
- **`validate_timestamps=0`** — disables file change checks between deployments
- **Sessions on `/tmp`** — local tmpfs instead of slow SMB home mount
- **`realpath_cache` 512k** — reduces `require_once` stat() overhead
- **mod_deflate** — gzip compression for HTML, CSS, JS, fonts, SVG
- **mod_expires** — 1-year cache for CSS/JS/fonts, 6-month for images, no-cache for HTML
- **Cache-Control `immutable`** — eliminates conditional GET round-trips for static assets
- **ETag `MTime Size`** — removes inode from ETag to prevent cache misses across Azure instances

---

## Security

- Passwords hashed with `password_hash()` / `password_verify()`
- `session_regenerate_id(true)` on login
- Whitelist-based redirect validation (no open redirects)
- Sensitive files (`.sql`, `.env`, `.sh`, `.log`) blocked via `.htaccess`
- `config/`, `models/`, `controllers/` directories blocked from direct HTTP access
- `X-Powered-By` header removed
- HTTPS enforced, TLS 1.2 minimum, `HttpOnly` + `Secure` + `SameSite=Strict` session cookies

---

## Contributors

- [A-olusanya](https://github.com/A-olusanya)
- [Arjun7161](https://github.com/Arjun7161)
