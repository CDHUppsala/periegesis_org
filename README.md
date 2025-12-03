Periegesis.org — Web App and Admin

### Overview
This repository contains the source code for the Periegesis.org website and its admin utilities. The stack is primarily a classic PHP application (custom framework) backed by MySQL, with a small modern frontend sub‑app powered by Vite + React for interactive map/visualization features.

The public web root is `htdocs_periegesis/`. The entry point `htdocs_periegesis/index.php` redirects users to a language‑scoped path based on the site configuration. Application code and shared includes live under the `sx_*` folders.

Key components:
- PHP site with multilang support configured in `sx_SiteConfig/sx_languages.php`
- MySQL connectivity via PDO in `sx_Conn/connMySQL.php`
- Admin tools and assets in `sx_Admin/`
- Reusable PHP utilities in `sx_php/`
- Frontend sub‑app (Vite/React) in `htdocs_periegesis/app_peripleo-pausanias/`


### Requirements
- Web server: Apache or Nginx (serve `htdocs_periegesis` as the document root)
- PHP: 7.4+ recommended (needs extensions: `pdo_mysql`, `mbstring`)
- Database: MySQL 5.7+ / MariaDB 10.3+
- Node.js and npm (for the Vite sub‑app): Node 18 LTS recommended (Vite 4.x compatible)

### Project Structure
- `htdocs_periegesis/` — Public web root
  - `index.php` — redirects to default language path
  - `en/`, `default.php`, and other content/app pages
  - `app_peripleo-pausanias/` — Vite/React frontend sub‑app (see below)
- `sx_Conn/`
  - `connMySQL.php` — PDO connection and DB credentials/constants
- `sx_SiteConfig/`
  - `sx_languages.php` — site URL, language settings, and flags
- `sx_Admin/` — admin UI, tools, vendor assets (e.g., TinyMCE, PHPMailer)
- `sx_Functions/`, `sx_Lang/`, `sx_Plugins/`, `sx_Scripts/`, `sx_Security/`, `sx_php/` — reusable PHP/JS utilities, language files, plugins, etc.
- `private/` — private/cache data and other non‑public assets

### Configuration and Environment
PHP configuration is done via PHP constants in code:
- `sx_SiteConfig/sx_languages.php`
  - `sx_Socket` (e.g., `https://`)
  - `sx_SiteURL` (e.g., `www.periegesis.abm.uu.se`)
  - `sx_radioCheckTrueSiteURL` (bool; enable true‑site check)
  - `sx_RadioMultiLang` (bool; multilang mode)
  - `sx_DefaultSiteLang` (e.g., `en`)
  - `sx_LangArr` (array of supported languages)
- `sx_Conn/connMySQL.php`
  - `sx_ServerName`, `sx_UserName`, `sx_Password`
  - `sx_TABLE_SCHEMA` (database name), `sx_Charset`


### Setup (Local/Dev)
1) Clone the repository
```
git clone <your-fork-or-repo-url>
cd periegesis.org
```

2) Configure the site
- Edit `sx_SiteConfig/sx_languages.php` and set:
  - `sx_Socket` and `sx_SiteURL` to match your dev host
  - `sx_DefaultSiteLang` and `sx_LangArr`
- Edit `sx_Conn/connMySQL.php` and set database credentials and schema.

3) Create the database
- Create a MySQL database named as configured in `sx_TABLE_SCHEMA` (default appears to be `ps_periegesis`).
- Import schema/data if available.

4) Configure your web server
- Point your virtual host document root to `<repo>/htdocs_periegesis`.
- Ensure PHP is enabled and has `pdo_mysql` and `mbstring` extensions.



### Build/Run (Production)
PHP application
- Deploy code to a server running Apache/Nginx + PHP.
- Set document root to `htdocs_periegesis`.
- Ensure `sx_SiteConfig/sx_languages.php` has the correct `sx_Socket` and `sx_SiteURL` for production.
- Ensure `sx_Conn/connMySQL.php` has production DB credentials (ideally injected via server env).


### Scripts
Frontend sub‑app (`htdocs_periegesis/app_peripleo-pausanias/package.json`):
- `npm run start` — Start Vite dev server
- `npm run build` — Production build (Vite)
- `npm run preview` — Preview the production build locally

There are no root‑level Node or Composer scripts.


### Entry Points and Routing
- Web entry: `htdocs_periegesis/index.php` loads `sx_SiteConfig/sx_languages.php` and redirects to `/<default-lang>/` based on `sx_DefaultSiteLang` and `sx_TrueSiteURL`.
- Admin and feature modules live under `sx_Admin/` and various `sx_*` folders and are typically included from PHP pages in `htdocs_periegesis`.


### Environment Variables
Currently not used directly. Sensitive configuration is in PHP constants. Recommended variables (if migrating):
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET`
- `APP_BASE_URL` (equivalent to `sx_Socket` + `sx_SiteURL`)


### Development Notes
- `.gitignore` excludes common folders like `/vendor/`, `/node_modules/`, build artifacts, logs, and `.env` files.
- Many admin/vendor assets (TinyMCE, PHPMailer, etc.) are stored within the repo under `sx_Admin/` and `htdocs_periegesis/dbAdmin/`.

