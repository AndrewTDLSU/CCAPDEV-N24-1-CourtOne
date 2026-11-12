# Phase-2 frontend integration

Based on Phase-2 commit `0781577`. The PHP views use controller data and normal
GET/POST forms instead of prototype HTML routes or browser-stored records.
The proposal's later page/action table (pages 45–47) is the integration reference.

## Run locally

Use PHP 8 with PDO MySQL and a MySQL database matching `database/courtOne_db.sql`.
Serve the **repository root**, not the `public` directory:

```powershell
$env:COURTONE_DB_HOST = 'localhost'
$env:COURTONE_DB_NAME = 'courtbooking'
$env:COURTONE_DB_USER = 'root'
$env:COURTONE_DB_PASSWORD = 'your-local-password'
php -S localhost:8000 -t .
```

Open `http://localhost:8000/index.php`. The existing local connection defaults
remain available in `core/Database.php`; environment variables override them.
Import the SQL seed only into a new/disposable database: it drops existing tables.
See `DEMO_CREDENTIALS.md` for seeded logins. Choose **Staff** for staff accounts,
or **Customer / Owner / Admin** for the other accounts.

## Connected flows

| Page | Controller route / behavior |
| --- | --- |
| Home, venue search | `home/index`, `venue/browseVenues`; database venues, search and sport filters |
| Login and signup | `auth/login`, `auth/signup`; validated credentials, sessions and role navigation |
| Venue details | `venue/viewVenue/{id}`; date navigation and database court availability |
| Booking | `booking/reserve`, `booking/payment/{id}`, `booking/bookingConfirmation/{id}` |
| Customer dashboard | `dashboard/userDashboard`, `booking/myBookings`; profile and booking history |
| Owner dashboard | `dashboard/ownerDashboard`; owner's venues and statistics links |
| Venue registration | `venue/venueForm`; details, pricing and optional image uploads |
| Venue statistics | `venue/venueStatistics/{id}`; revenue, bookings, court usage, year/month filters and court creation |
| Staff management | `staff/showAllStaff`, `staff/createStaff`; venue filter and account creation |
| Staff dashboard | `dashboard/staffDashboard`, `dashboard/changeBookingStatus`; assigned venue bookings and status updates |
| Admin dashboard | `dashboard/adminDashboard`, `admin/deleteUser/{id}`, `admin/deleteVenue/{id}`; records, reservation totals and deletion feedback |
| Chat | `chat/index`; stored two-user messages, escaped on output |

POST forms carry a session CSRF token. Authenticated actions use the signed-in
user instead of the temporary IDs in the base commit. Booking times use
Asia/Manila. Payments and refunds remain demonstrations; no gateway is called.

The wider proposal also describes venue editing, owner deletion, staff deletion,
profile editing, role changes, blocked slots, rescheduling and attendance flags.
Those do not have complete controller flows in this phase and are not implemented
by this frontend integration. Unsupported prototype controls remain disabled.
The existing customer cancellation controller is preserved, although the earlier
proposal description says cancellation should go through staff.

## Verification

Run `python tests/smoke.py` with PHP on PATH. It lints PHP, renders 16 pages with
both populated and empty data at three deployment paths (96 renders), checks local assets,
and rejects old HTML links.
It does not connect to or modify a database.

Integration was also checked over HTTP using an isolated seeded MySQL database:
registration; all role logins; venue/court/staff creation; booking, demo payment
and confirmation; chat persistence; staff status changes; admin deletion and
history/self-deletion protection; role restrictions. A browser check confirmed
mobile slot selection updates the reservation form. The regular local database
was not seeded or modified.

## XAMPP / Apache and subfolder deployment

Copy the repository to `C:/xampp/htdocs/CourtOne` and start Apache and MySQL
in the XAMPP control panel. Open `http://localhost/CourtOne/`.
`htdocs` is the default document root; `xampp/apache` holds Apache itself.
Use PHP 8 or newer. Import `database/courtOne_db.sql` into a **new** database;
the seed drops tables, so never import over existing records.

Set database environment variables in Apache's directory or virtual-host config,
for example for a fresh local XAMPP database:

```apache
<Directory "C:/xampp/htdocs/CourtOne">
    SetEnv COURTONE_DB_HOST 127.0.0.1
    SetEnv COURTONE_DB_PORT 3306
    SetEnv COURTONE_DB_NAME courtone_local_demo
    SetEnv COURTONE_DB_USER root
    SetEnv COURTONE_DB_PASSWORD ""
</Directory>
```

Restart Apache after changing its configuration. Keep actual credentials out of Git.
Asset URLs, form actions, navigation, image paths and redirects now derive their
prefix from the executing script. Both `/CourtOne/` and root deployments work.
Serve the repository root, including `public` and `bootstrap-5.3.8-dist`.

If a reverse proxy adds or strips a prefix, set `COURTONE_BASE_PATH` to the
browser-visible application path (for example `/class/team`, or `/` for root)
in Apache's configuration. Proxmox itself does not serve CSS: verify the guest
web server and proxy mappings. In browser Network tools, CSS requests must return
200 with `text/css`, not a 404 or an HTML login/error page. Linux filenames are
case-sensitive, so preserve the repository's exact filename casing.

If standard ports are occupied, change Apache `Listen`/`ServerName` and the MySQL
port in XAMPP configuration, then match `COURTONE_DB_PORT` to the chosen port.
Run `php tests/paths.php` to check automatic paths and the proxy override.

The SQL seed uses `utf8mb4_unicode_ci` throughout for compatibility with both
MySQL and XAMPP MariaDB. Existing databases are not migrated by this change.
