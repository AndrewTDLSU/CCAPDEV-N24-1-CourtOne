"""Render each integrated view with populated and empty fixtures; no database writes."""
from pathlib import Path
import subprocess, tempfile, re
root = Path(__file__).resolve().parents[1]
views = ["auth/index", "auth/login", "auth/signup", "dashboards/user", "dashboards/owner", "dashboards/staff", "dashboards/admin", "venues/browseVenues", "venues/viewVenue", "venues/venueForm", "venues/manageStaff", "venues/manageVenue", "bookings/payment", "bookings/bookingConfirmation", "bookings/myBookings", "chats/chat"]
for file in root.rglob("*.php"):
    subprocess.run(["php", "-l", str(file)], check=True, capture_output=True)
for empty in [0, 1]:
    for view in views:
        code = "<?php $_GET['empty']=" + str(empty) + "; require '" + (root / "tests/view-fixture.php").as_posix() + "'; require '" + (root / ("views/" + view + ".php")).as_posix() + "';"
        with tempfile.TemporaryDirectory() as folder:
            script = Path(folder) / "render.php"
            script.write_text(code, encoding="utf-8")
            result = subprocess.run(["php", str(script)], check=True, capture_output=True)
        html = result.stdout.decode("utf-8")
        assert not result.stderr, result.stderr.decode()
        for asset in re.findall(r'(?:src|href)="(/(?:public|bootstrap-5.3.8-dist)/[^"?]+)"', html):
            assert (root / asset.lstrip("/")).is_file(), (view, asset)
        assert not re.search(r'(?:href|action)="[^"<>]*\.html', html), view
print("PASS: PHP lint, 32 view renders, local assets, and MVC links")
