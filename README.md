Periegesis.org — Web App and Admin

### Overview
This repository contains the source code for the Periegesis.org website and its admin utilities. The stack is primarily a classic PHP application (custom framework) backed by MySQL, with a small modern frontend sub‑app powered by Vite + React for interactive map/visualization features.

The public web root is `public_html/`. The entry point `public_html/index.php` redirects users to a language‑scoped path based on the site configuration. Application code and shared includes live under the `sx_*` folders.

Key components:
- PHP site with multilang support configured in `sx_SiteConfig/sx_languages.php`
- MySQL connectivity via PDO in `sx_Conn/connMySQL.php`
- Admin tools and assets in `sx_Admin/`
- Reusable PHP utilities in `sx_php/`
- Frontend sub‑app (Vite/React) in `public_html/sxApps/ps_maps/`


### Requirements
- Web server: Apache or Nginx (serve `public_html` as the document root)
- PHP: 8.2 recommended (needs extensions: `pdo_mysql`, `mbstring`, `gd`, `zip`)
- Database: MySQL 8.0+
- Node.js and npm (for the Vite sub‑app): Node 18 LTS recommended (Vite 4.x compatible)

### Project Structure
- `public_html/` — Public web root
  - `index.php` — redirects to default language path
  - `en/`, `default.php`, and other content/app pages
  - `sxApps/ps_maps/` — Vite/React frontend sub‑app (see below)
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


### Docker Setup (Recommended)
The easiest way to run the project locally is using Docker and Docker Compose. This setup automatically configures the PHP environment and a MySQL 8.0 database.

**Prerequisites:**
- Docker and Docker Compose installed.

**Steps:**
1. **Clone the repository:**
   ```bash
   git clone <your-repo-url>
   cd periegesis_org
   ```

2. **Start the containers:**
   ```bash
   docker-compose up -d
   ```
   This will:
   - Build the PHP 8.2 Apache image.
   - Start a MySQL 8.0 container.
   - Automatically initialize the database using `sql/setup_ps_uu_periegesis.sql`.

3. **Access the application:**
   - Website: [http://localhost:8044](http://localhost:8044)
   - The default configuration redirects `localhost:8044` to the language-scoped path (e.g., `/en/`).

**Note on Database Initialization:**
The first time you run `docker-compose up`, the database is initialized. If you need to re-initialize it (e.g., after modifying the SQL dump), run:
```bash
docker-compose down -v
docker-compose up -d
```

### Configuration (Environment Variables)
The Docker setup uses environment variables to configure the application. These are defined in `docker-compose.yml`:

| Variable | Description | Default in Compose |
| --- | --- | --- |
| `DB_HOST` | Database hostname | `db` |
| `DB_NAME` | Database schema name | `ps_uu_periegesis` |
| `DB_USER` | Database username | `ps_uu_DigitalPeriegesis` |
| `DB_PASS` | Database password | `ps_uu_V453-O821-D974` |
| `SITE_SOCKET` | Protocol (http:// or https://) | `http://` |
| `SITE_URL` | Site hostname and port | `localhost:8044` |
| `CHECK_TRUE_SITE`| Enable canonical URL check | `false` |

### Setup (Manual/Local)
If you prefer not to use Docker, follow these steps:

1) Clone the repository
```bash
git clone <your-fork-or-repo-url>
cd periegesis_org
```

2) Configure the site
- Edit `sx_SiteConfig/sx_languages.php` and set:
  - `sx_Socket` and `sx_SiteURL` to match your dev host.
- Edit `sx_Conn/connMySQL.php` and set database credentials and schema.

3) Create the database
- Create a MySQL 8.0 database named `ps_uu_periegesis`.
- Import `sql/setup_ps_uu_periegesis.sql`.

4) Configure your web server
- Point your virtual host document root to `<repo>/public_html`.
- Ensure PHP 8.0+ is enabled with `pdo_mysql`, `mbstring`, `gd`, and `zip` extensions.



### Deployment
Deployment can be achieved using Docker (recommended) or manual setup.

#### Docker Deployment
- Use the provided `Dockerfile` and `docker-compose.yml`.
- Configure the environment variables (see above) to match your production domain.
- In production, it's recommended to set `CHECK_TRUE_SITE=true` to enforce canonical URL redirection.

#### Manual Deployment
1. Deploy code to a server running Apache/Nginx + PHP 8.0+.
2. Set the document root to the `public_html` directory.
3. Configure `sx_SiteConfig/sx_languages.php` and `sx_Conn/connMySQL.php`.
4. Ensure the server environment variables are set or fallback constants are correct.


### Scripts
Frontend sub‑app (`public_html/sxApps/ps_maps/package.json`):
- `npm run start` — Start Vite dev server
- `npm run build` — Production build (Vite)
- `npm run preview` — Preview the production build locally

There are no root‑level Node or Composer scripts.


### Entry Points and Routing
- Web entry: `public_html/index.php` loads `sx_SiteConfig/sx_languages.php` and redirects to `/<default-lang>/` based on `sx_DefaultSiteLang` and `sx_TrueSiteURL`.
- Admin and feature modules live under `sx_Admin/` and various `sx_*` folders and are typically included from PHP pages in `public_html`.


### Environment Variables
The application supports configuration via environment variables (primarily used in Docker). If not set, it falls back to constants defined in the PHP files.
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET`
- `SITE_SOCKET`, `SITE_URL`, `CHECK_TRUE_SITE`

### Development Notes
- `.gitignore` excludes common folders like `/node_modules/`, build artifacts, logs, and `.env` files.
- Many admin/vendor assets (TinyMCE, PHPMailer, etc.) are stored within the repo under `sx_Admin/` and `public_html/dbAdmin/`.

