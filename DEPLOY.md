# Deployment — cPanel shared hosting (Git Version Control)

Deploy method: **cPanel → Git Version Control**. cPanel pulls this private repo
over SSH and runs [`.cpanel.yml`](.cpanel.yml) on each deploy. There is **no
build server**, so front-end assets are built locally and committed
(`public/build`).

Concrete values for this deployment:

| Setting            | Value                                          |
|--------------------|------------------------------------------------|
| cPanel user        | `wipxioco`                                      |
| GitHub repo        | `khairat-hossin/barakah`                        |
| Subdomain          | `eou.wipxio.com`                                |
| App/Repository path| `/home/wipxioco/eou.wipxio.com` (outside `public_html`)|
| Document Root      | `/home/wipxioco/eou.wipxio.com/public`                 |

---

## One-time setup

### 1. SSH auth to GitHub (already done on this server)
cPanel already authenticates to GitHub with the account-level key
`~/.ssh/cpanel_all_repos` (verify with `ssh -T git@github.com` → *Hi khairat-hossin!*).
So **no new deploy key is needed** — that key already grants access to all your repos.
Confirm access to this one:
```bash
git ls-remote git@github.com:khairat-hossin/barakah.git   # should list refs
```

### 2. Clone + subdomain
1. cPanel → **Git Version Control → Create**
   - Clone URL: `git@github.com:khairat-hossin/barakah.git`
   - Repository Path: `/home/wipxioco/eou.wipxio.com`  ← **outside** `public_html`
2. cPanel → **Domains / Subdomains** → for `eou.wipxio.com` set the
   - **Document Root: `/home/wipxioco/eou.wipxio.com/public`**  ← must be the `public/` subfolder.
   *(The auto-created `~/public_html/eou.wipxio.com` folder is unused — you can delete it.)*
3. cPanel → **MultiPHP Manager** → set the subdomain to **PHP 8.3 or 8.4**
4. **Select PHP Version → Extensions**: enable
   `gd, mbstring, zip, intl, bcmath, curl, fileinfo, exif, pdo_mysql, openssl, xml, tokenizer, ctype`
5. cPanel → **MultiPHP INI Editor** (this subdomain):
   `upload_max_filesize=10M`, `post_max_size=12M`, `memory_limit=256M`
   *(Required — the default 2M silently rejects logo uploads.)*

### 3. Database
cPanel → **MySQL Databases** → create a database + user, add user to DB with **All Privileges**.
Note the `wipxioco_`-prefixed names (e.g. `wipxioco_barakah`) for the `.env`.

### 4. Edit `.cpanel.yml` paths
In [`.cpanel.yml`](.cpanel.yml) set `DEPLOYPATH`, `PHP`, and `COMPOSER` to match your host,
then commit + push. (Find PHP path with `which php` or use the `ea-php83` path; composer with `which composer`.)

### 5. First deploy + server bootstrap
1. cPanel → Git Version Control → **Manage → Pull or Deploy → Update from Remote**, then **Deploy HEAD Commit**.
2. cPanel → **Terminal**:
   ```bash
   cd ~/eou.wipxio.com
   cp .env.production.example .env      # then edit real values
   php artisan key:generate
   mkdir -p public/branding storage/app/mpdf
   chmod -R 775 storage bootstrap/cache
   php artisan storage:link
   php artisan migrate --force
   php artisan db:seed --force          # FIRST TIME ONLY (RBAC, chart of accounts, admin, org profile)
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
3. Log in with the `ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env`, then remove those seed vars.

### 6. Email queue (choose one)
- **Cron worker (recommended)** — cPanel → **Cron Jobs**, every minute:
  ```
  * * * * * cd /home/wipxioco/eou.wipxio.com && /usr/local/bin/php artisan queue:work --stop-when-empty --max-time=55 >/dev/null 2>&1
  ```
- **Or** set `QUEUE_CONNECTION=sync` in `.env` (emails send during the request; no cron).

### 7. HTTPS
cPanel → **SSL/TLS Status** → run **AutoSSL** for the subdomain. Confirm `APP_URL=https://...`.

### 8. Verify the deploy (health checks)
1. **Browser:** `https://eou.wipxio.com/up` → should return HTTP 200 (Laravel's built-in framework check).
2. **Deeper check** (Terminal, or read it in the deploy log — `.cpanel.yml` runs it automatically):
   ```bash
   cd ~/eou.wipxio.com && php artisan app:health
   ```
   Confirms: APP_KEY set, `production`/debug-off/https, **database connects**, **migrations ran**,
   **storage & bootstrap/cache writable**, cache works, **Vite build present**, PDF font present,
   storage symlink. Exits non-zero (and fails the deploy) if a critical check fails.

---

## Every deploy after that

```bash
# locally
npm run build            # only if JS/CSS changed
git add -A && git commit -m "..." && git push
```
Then cPanel → **Git Version Control → Update from Remote → Deploy HEAD Commit**.
(`.cpanel.yml` runs composer install, migrations, and cache rebuilds automatically.)

---

## Gotchas (this project)
- **`public/build` is committed** — no Node on the host. If the site loads unstyled, you forgot `npm run build` + commit.
- **`.env` lives only on the server** (git-ignored) and persists across deploys.
- **`public/branding/`** and **`storage/app/mpdf`** must exist + be writable (created above) — uploaded logos and PDF temp.
- Uploaded logos must be **PNG/JPG** (mPDF can't render SVG); the app auto-resizes them.
- After any manual `.env` change on the server: `php artisan config:cache`.
