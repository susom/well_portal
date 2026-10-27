# WELL for Life portal: notes for whoever comes next

Last touched **2026-10-27** by Irvin Szeto (irvins@stanford.edu), working with Claude Code.
The project has no funding and nobody maintains it, but participants still log in and use it.
If you're reading this, something probably broke or a new PHP/OS vulnerability came out.
This page should get you oriented in about 15 minutes.

## What it is
- A 2016 PHP site (no framework) that sits in front of Stanford REDCap. Participants log in, take yearly
  WELL surveys, and see their radar-chart results, points, games and rewards.
- **There's no database of its own.** Every user and every answer lives in REDCap and is read and written through
  the REDCap API (`models/class.RCAPI.php`). The tokens are in `models/my_settings.php`. That file is gitignored,
  lives only on the server, and **must never be printed or committed**.
- REDCap projects:

| Project | PID |
|---|---|
| REDCAP_PORTAL (users and core surveys) | 8238 |
| Supp | 8254 |
| SHORT_SCALE | 11796 |
| ADMIN_CMS (site content, images, points, quotes) | 12482 |
| POLL_PROJECT | 17031 |
| foodquestions | 8197 |
| miniintervention | 10119 |
| taiwan_admin | 10057 |

- **To edit site content**, use REDCap PID 12482. The old `cms.php` page is disabled on purpose: it had no admin
  check and let anyone act as any user.

## Where it runs
- **GCP project:** `som-well-for-life`
- **VM:** `wellforlife-vm1`, zone us-west1-b, Container-Optimized OS. Reach it over SSH through IAP:
  `gcloud compute ssh wellforlife-vm1 --zone=us-west1-b --project=som-well-for-life --tunnel-through-iap`
- **Front end:** a GCP HTTPS load balancer at 130.211.17.145, with a Google-managed certificate for
  `wellforlife-portal.stanford.edu`. It renews automatically.
- **Container:** one Docker container named `well-portal`. The image (built from `Dockerfile`) supplies PHP-FPM,
  nginx and supervisord.
  - **The code is not in the image at runtime.** It's a git checkout of the `well-prod` branch inside the named
    volume `well-portal`, which is mounted at `/var/www/html`.
  - `models/my_settings.php` also lives in that volume.
- **Persistent disk:** `/mnt/disks/ext-disk` holds:
  - `well-portal-ssh`: the deploy key for git pulls, mounted read-only.
  - `well-portal-radar-backup`: radar CSVs from before 2026, owned by root and not public.
  - `wellforlife-deploy`: the original 2018 run scripts, kept for history.

## Deploying code (the normal case)
Push to `well-prod`. GitHub Actions (`.github/workflows/deploy.yml`) then SSHes in, runs `git pull` inside
the container, and reloads php-fpm. That's the whole deploy. The `well-dev` branch is stale, so ignore it.

## Patching PHP / rebuilding the image (when a PHP CVE lands)
1. In `Dockerfile`, bump `FROM php:<version>-fpm-alpine` to the newest patch release.
2. Build for the VM's CPU and name the tag after the PHP version:
   `docker build --platform linux/amd64 -t well-portal:php<version>-rN .`
3. Ship it to the VM. There's no registry; the image goes straight over SSH:
   `docker save well-portal:<tag> | gzip | gcloud compute ssh wellforlife-vm1 --zone=us-west1-b --project=som-well-for-life --tunnel-through-iap -- 'gunzip | docker load'`
4. Swap the container (about 5s downtime). If login.php doesn't return 200, it reverts on its own:
   `gcloud compute ssh wellforlife-vm1 --zone=us-west1-b --project=som-well-for-life --tunnel-through-iap -- 'NEW=well-portal:<tag> bash -s' < docker/swap.sh`
5. Once the new image works, delete the old one on the VM with `docker rmi`.

Local dev: run `docker compose up --build` and open http://localhost:7070. **The local copy of `my_settings.php`
points at real prod REDCap**, so don't submit surveys or change accounts while testing.

## Things that will expire on their own
- **PHP 8.5 security support ends around 31 Dec 2029.** After that, patching means moving to a newer PHP, which
  probably means fixing some deprecations. Check https://www.php.net/supported-versions.php
- **The VM's Container-Optimized OS release** will reach end of life. You'd upgrade it by recreating the VM from a
  newer COS image, reattaching the persistent disk, and recreating the `well-portal` volume from git plus `my_settings.php`.
- **The REDCap API:** if Stanford REDCap changes or retires API features, logins and surveys break.
- **API tokens** belong to whoever generated them in REDCap. If that person leaves Stanford, the tokens may stop
  working, and then nobody can log in. In that case, generate new tokens in each project's API page and replace
  them in `my_settings.php` on the server.

## What was done on 2026-10-27
- **PHP 8.5.12**, which fixes the CISA-listed "php2shell" bugs (CVE-2026-109618/109619). The current image is `well-portal:php8.5.12-r3`.
- **A `?lang=` file-inclusion hole that was being actively exploited** is fixed.
  - Password reset now requires a verified session.
- **`cms.php` is blocked.**
  - Radar CSVs moved out of the webroot; `radar_csv.php` serves each user only their own.
  - Results pages emailed before July 2019 no longer show their charts.
- **Every REDCap API token was rotated** after an exposure.
  - The email relay token in `my_settings.php` (from the Email Relay external module) was also exposed and should be regenerated.
  - Part of the reCAPTCHA secret was exposed too; rotate it if that hasn't happened.
- **About 2× faster.** Project-wide REDCap calls are cached on disk under `/tmp/well_cache`, for 10 minutes to 1 day.
- **Smaller fixes:**
  - `ajax_handler.php` requires login.
  - nginx denies `/models/` and `/docker/`.
  - pearcmd is removed from the image.
  - TLS verification is back on.
  - The registration temp password is random, and login refuses the old guessable one.
  - The never-working participant-ID login is removed.

## Known weaknesses, not fixed (each needs a real project)
- No CSRF tokens. Several XSS spots. Forms write whatever fields are posted (mass assignment).
- Passwords are stored as salted SHA1. They could be moved to `password_hash` by upgrading each one when its user next logs in.
- The failed-login limit only counts per browser session.
- Login downloads all ~15k user records from REDCap to find one user, which takes about 2s.
- `rewards.php` is 2.7 MB because its images are inlined as base64.
- `/var/log/redcap_portal.log` (`LOG_FILE`) isn't writable, so `logIt()` writes nothing and leaves a PHP warning in the
  container log instead. That's harmless; check `docker logs well-portal` for errors.

## Logs
- `docker logs well-portal` shows the nginx access and error logs plus PHP warnings.
  `docker/swap.sh` keeps them rotated (10 MB × 5 files). There's no cron job.
- History: the 2016–2026 image wrote PHP errors to a file inside the container (`.../logs/php7/error.log`), which grew
  until the site slowed down, so a cron job emptied it every few weeks. The current image writes no such file:
  PHP errors go to stderr, and Docker rotates them. **Don't make `LOG_FILE` writable or point `error_log` at a file**
  unless you also set up rotation.
- The load balancer's request logs are in Cloud Logging for project `som-well-for-life`.
