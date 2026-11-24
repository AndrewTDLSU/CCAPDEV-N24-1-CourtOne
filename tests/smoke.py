"""Render each integrated view with populated and empty fixtures; no database writes."""
from pathlib import Path
import os, subprocess, tempfile, re
from html import unescape
root = Path(__file__).resolve().parents[1]
views = ["auth/index", "auth/login", "auth/signup", "dashboards/user", "dashboards/owner", "dashboards/staff", "dashboards/admin", "venues/browseVenues", "venues/viewVenue", "venues/venueForm", "venues/manageStaff", "venues/manageVenue", "venues/manageVenues", "dashboards/venue", "bookings/payment", "bookings/bookingConfirmation", "chats/chat"]
env = os.environ.copy()
env.pop("COURTONE_BASE_PATH", None)
env.pop("COURTONE_MEDIA_BASE_URL", None)
for file in root.rglob("*.php"):
    subprocess.run(["php", "-l", str(file)], check=True, capture_output=True)
for script_name, prefix in [('/index.php', ''), ('/CourtOne/index.php', '/CourtOne'), ('/class/team/public/index.php', '/class/team')]:
    for empty in [0, 1]:
        for view in views:
            code = "<?php $_GET['empty']=" + str(empty) + "; require '" + (root / "tests/view-fixture.php").as_posix() + "'; $_SERVER['SCRIPT_NAME']='" + script_name + "'; require '" + (root / ("views/" + view + ".php")).as_posix() + "';"
            with tempfile.TemporaryDirectory() as folder:
                script = Path(folder) / "render.php"
                script.write_text(code, encoding="utf-8")
                result = subprocess.run(["php", str(script)], capture_output=True, env=env)
            html = result.stdout.decode("utf-8")
            assert result.returncode == 0 and not result.stderr, (view, result.stderr.decode())
            for element in ("html", "head", "body", "main"):
                assert len(re.findall(rf"<{element}\b", html)) == 1, (view, element)
            assert html.count('aria-label="Main navigation"') == 1, view
            assert html.count("public/js/theme.js") == 1, view
            assert html.count("bootstrap.bundle.min.js") == 1, view
            assert html.count('id="darkModeBtn"') == 1, view
            ids = re.findall(r'\sid="([^"]+)"', html)
            assert len(set(ids)) == len(ids), view
            if view == "dashboards/user":
                assert f'{prefix}/index.php?url=dashboard/userDashboard#bookings' in html
                for section in ("dashboard", "bookings", "profile"):
                    assert section in ids and f'href="#{section}"' in html
                assert ("?url=booking/payment/1" in html) == (not empty)
                assert ("?url=booking/cancelBooking/1" in html) == (not empty)
            for url in re.findall(r'(?:src|href|action)="(/[^"<>]*)"', html):
                url = unescape(url)
                assert url.startswith(prefix + '/'), (view, script_name, url)
                local = url[len(prefix):].split('?')[0].split('#')[0]
                if local.startswith(('/public/', '/bootstrap-5.3.8-dist/')):
                    assert (root / local.lstrip('/')).is_file(), (view, url)
            assert not re.search(r'(?:href|action)="[^"<>]*\.html', html), view
print(f"PASS: PHP lint, {len(views) * 6} view renders at root/nested/public entry points, assets, forms, and MVC links")
