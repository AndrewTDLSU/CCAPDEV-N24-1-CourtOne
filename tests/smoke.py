"""Render each integrated view with populated and empty fixtures; no database writes."""
from pathlib import Path
import subprocess, tempfile, re
from html import unescape
root = Path(__file__).resolve().parents[1]
views = ["auth/index", "auth/login", "auth/signup", "dashboards/user", "dashboards/owner", "dashboards/staff", "dashboards/admin", "venues/browseVenues", "venues/viewVenue", "venues/venueForm", "venues/manageStaff", "venues/manageVenue", "bookings/payment", "bookings/bookingConfirmation", "bookings/myBookings", "chats/chat"]
for file in root.rglob("*.php"):
    subprocess.run(["php", "-l", str(file)], check=True, capture_output=True)
for script_name, prefix in [('/index.php', ''), ('/CourtOne/index.php', '/CourtOne'), ('/class/team/public/index.php', '/class/team')]:
    for empty in [0, 1]:
        for view in views:
            code = "<?php $_GET['empty']=" + str(empty) + "; require '" + (root / "tests/view-fixture.php").as_posix() + "'; $_SERVER['SCRIPT_NAME']='" + script_name + "'; require '" + (root / ("views/" + view + ".php")).as_posix() + "';"
            with tempfile.TemporaryDirectory() as folder:
                script = Path(folder) / "render.php"
                script.write_text(code, encoding="utf-8")
                result = subprocess.run(["php", str(script)], check=True, capture_output=True)
            html = result.stdout.decode("utf-8")
            assert not result.stderr, result.stderr.decode()
            for url in re.findall(r'(?:src|href|action)="(/[^"<>]*)"', html):
                url = unescape(url)
                assert url.startswith(prefix + '/'), (view, script_name, url)
                local = url[len(prefix):].split('?')[0].split('#')[0]
                if local.startswith(('/public/', '/bootstrap-5.3.8-dist/')):
                    assert (root / local.lstrip('/')).is_file(), (view, url)
            assert not re.search(r'(?:href|action)="[^"<>]*\.html', html), view
print("PASS: PHP lint, 96 view renders at root/nested/public entry points, assets, forms, and MVC links")
