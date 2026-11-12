(() => {
    // Keep dashboard variables and handlers out of the global scope.
    "use strict";

    // Seed data lets the admin page demonstrate venue management before any venues are added.
    // The venue form and detail page use the same initial sample records.
    const sampleVenues = [
        { id: "pasay-sports-complex", name: "Pasay Sports Complex", sport: "Basketball", location: "GXWV+39H, Derham, Pasay City, Metro Manila", description: "Community sports courts in Pasay City.", courts: 4, price: 800, opens: "06:00", closes: "22:00", image: "media/venues/pasay-sports-complex.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "playground-cloverleaf", name: "The Playground by Cloverleaf", sport: "Multi-sport", location: "2499-2545 P. Zamora St, Manila, Metro Manila", description: "Flexible indoor sports venue near Cloverleaf.", courts: 3, price: 1000, opens: "07:00", closes: "23:00", image: "media/venues/playground-by-cloverleaf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "koliseum-ng-bayan", name: "Sports Complex (Koliseum ng Bayan)", sport: "Basketball", location: "H236+VHR, Washington, Makati City, Metro Manila", description: "Local sports complex with bookable courts.", courts: 2, price: 700, opens: "06:00", closes: "21:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking"], active: true },
        { id: "ph-arena", name: "PH Arena", sport: "Basketball", location: "Bulacan", description: "Basketball courts at the Philippine Arena sports venue.", courts: 4, price: 1200, opens: "06:00", closes: "22:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "gymnasium", name: "Gymnasium", sport: "Gymnastics", location: "Quezon City", description: "Indoor gymnastics facility with dedicated training space.", courts: 2, price: 900, opens: "07:00", closes: "21:00", image: "https://upload.wikimedia.org/wikipedia/commons/9/94/Shimonoseki_Gymnastics.jpg", imageCredit: "Photo by Bsx, CC0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:Shimonoseki_Gymnastics.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "araneta-coliseum", name: "Araneta Coliseum", sport: "Badminton", location: "Quezon City", description: "Indoor badminton courts in Quezon City.", courts: 4, price: 850, opens: "06:00", closes: "22:00", image: "https://upload.wikimedia.org/wikipedia/commons/1/1b/HK_SWCC_Sheung_Wan_Sports_Centre_12th_floor_interior_badmanton_courts_visitors_April_2017_IX1.jpg", imageCredit: "Photo by JIMZHEUNGWie, CC BY-SA 4.0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:HK_SWCC_Sheung_Wan_Sports_Centre_12th_floor_interior_badmanton_courts_visitors_April_2017_IX1.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "sports-complex", name: "Sports Complex", sport: "Futsal", location: "Pasig", description: "Futsal courts with artificial turf in Pasig.", courts: 2, price: 1000, opens: "06:00", closes: "22:00", image: "https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg/960px-DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg", imageCredit: "Photo by PattayaPatrol, CC BY-SA 4.0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true }
    ];

    // Read JSON-backed demo records, using the fallback only when the key is absent.
    function readList(key, fallback = []) {
        // Parse the saved list or serialize and parse the fallback into a fresh array.
        return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
    }

    // Escape values inserted into table markup so stored user-entered text stays text.
    function escapeHtml(value) {
        // Replace HTML-significant characters with entities before building table rows.
        return String(value ?? "").replace(/[&<>"']/g, (character) => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
        })[character]);
    }

    // Use the venue table as the marker that this script is running on the dashboard.
    const venueRows = document.getElementById("venue-rows");
    // This script is shared by the site but should act only on the admin dashboard.
    if (!venueRows) return;

    // Load saved venue records and determine which sample entries need merging.
    const storedVenues = localStorage.getItem("courtOneVenues");
    let venues = readList("courtOneVenues", sampleVenues);
    const missingSamples = sampleVenues.filter((sample) =>
        !venues.some((venue) => venue.id === sample.id)
    );
    const futsalSample = sampleVenues.find((sample) => sample.id === "sports-complex");
    const existingFutsal = venues.find((venue) => venue.id === "sports-complex");
    // Detect the older futsal sample image so stored demo data can be migrated.
    const updatedFutsalImage = existingFutsal?.image ===
        "https://upload.wikimedia.org/wikipedia/commons/8/8b/DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg";
    if (updatedFutsalImage) {
        existingFutsal.image = futsalSample.image;
    }
    if (!storedVenues || missingSamples.length > 0 || updatedFutsalImage) {
        venues = storedVenues ? [...venues, ...missingSamples] : venues;
        localStorage.setItem("courtOneVenues", JSON.stringify(venues));
    }

    // Recalculate summary cards and rebuild all data tables from current browser storage.
    function renderDashboard() {
        // Load the latest account and reservation lists on every render.
        const users = readList("courtOneUsers");
        const bookings = readList("courtOneBookings");
        // Exclude unlisted venues from the public-venue summary count.
        const activeVenues = venues.filter((venue) => venue.active !== false);
        // Update the four summary values displayed above the selected panel.
        document.getElementById("user-count").textContent = users.length;
        document.getElementById("owner-count").textContent = users.filter((user) => user.role === "owner").length;
        document.getElementById("venue-count").textContent = activeVenues.length;
        document.getElementById("booking-count").textContent = bookings.length;

        // Rebuild the dashboard summaries and tables from the latest browser-stored records.
        venueRows.innerHTML = venues.map((venue) => `
            <tr>
                <td><div class="d-flex align-items-center gap-2"><img class="venue-thumb rounded" src="${escapeHtml(venue.image)}" alt=""><span class="venue-name fw-semibold">${escapeHtml(venue.name)}</span></div></td>
                <td>${escapeHtml(venue.sport)}</td><td>${escapeHtml(venue.location)}</td>
                <td><span class="badge ${venue.active === false ? "text-bg-secondary" : "text-bg-success"}">${venue.active === false ? "Unlisted" : "Listed"}</span></td>
                <td><div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-primary" href="viewVenue.html?id=${encodeURIComponent(venue.id)}">View</a><a class="btn btn-sm btn-outline-secondary" href="venueForm.html?id=${encodeURIComponent(venue.id)}">Edit</a><button class="btn btn-sm btn-outline-dark" type="button" data-toggle-venue="${escapeHtml(venue.id)}">${venue.active === false ? "Restore" : "Unlist"}</button></div></td>
            </tr>`).join("");
        document.getElementById("empty-venues").classList.toggle("d-none", venues.length > 0);

        // Render account identity, role, active status, and its toggle control.
        const userRows = document.getElementById("user-rows");
        userRows.innerHTML = users.map((user) => `
            <tr>
                <td><span class="user-name">${escapeHtml(user.name)}</span></td>
                <td class="user-email">${escapeHtml(user.email)}</td>
                <td>${user.role === "owner" ? "Venue owner" : "Customer"}</td>
                <td><span class="user-status ${user.active === false ? "is-inactive" : "is-active"}">${user.active === false ? "Inactive" : "Active"}</span></td>
                <td class="user-action-cell"><button class="user-action" type="button" data-toggle-user="${escapeHtml(user.id)}" aria-label="${user.active === false ? "Activate" : "Deactivate"} ${escapeHtml(user.name)}" title="${user.active === false ? "Activate" : "Deactivate"} account"><span aria-hidden="true">⌄</span></button></td>
            </tr>`).join("");
        document.getElementById("empty-users").classList.toggle("d-none", users.length > 0);

        // Show the eight most recent bookings, newest first.
        const bookingRows = document.getElementById("booking-rows");
        bookingRows.innerHTML = bookings.slice().reverse().slice(0, 8).map((booking) => `
            <tr><td>${escapeHtml(booking.venueName)}</td><td>${escapeHtml(booking.date)}</td><td>Court ${escapeHtml(booking.court)} · ${escapeHtml(booking.time)}</td></tr>`).join("");
        document.getElementById("empty-bookings").classList.toggle("d-none", bookings.length > 0);
    }

    // Cache tabs and their corresponding panels so selecting a tab can update both.
    const dashboardTabs = Array.from(document.querySelectorAll(".dashboard-tabs [role='tab']"));
    const dashboardTabList = document.querySelector(".dashboard-tabs");
    const dashboardPanels = dashboardTabs
        .map((tab) => document.getElementById(tab.getAttribute("aria-controls")))
        .filter(Boolean);

    // Match the accessibility orientation to the desktop or mobile tab layout.
    function updateTabOrientation() {
        dashboardTabList?.setAttribute(
            "aria-orientation",
            matchMedia("(max-width: 800px)").matches ? "horizontal" : "vertical"
        );
    }

    // Select one tab, hide all other panels, and optionally update URL and keyboard focus.
    function activateDashboardTab(tab, updateHistory = false, moveFocus = false) {
        const panelId = tab.getAttribute("aria-controls");
        dashboardTabs.forEach((item) => {
            const isSelected = item === tab;
            item.classList.toggle("is-current", isSelected);
            item.setAttribute("aria-selected", String(isSelected));
            item.tabIndex = isSelected ? 0 : -1;
        });
        dashboardPanels.forEach((panel) => {
            panel.hidden = panel.id !== panelId;
        });
        if (updateHistory && location.hash !== tab.hash) {
            history.pushState(null, "", tab.hash);
        }
        if (moveFocus) tab.focus();
    }

    // Restore the selected panel from the URL hash, defaulting to Manage Users.
    function activateTabFromLocation() {
        const tab = dashboardTabs.find((item) => item.hash === location.hash) || dashboardTabs[0];
        activateDashboardTab(tab);
    }

    // Switch panels when a dashboard tab is clicked, without navigating away.
    dashboardTabList?.addEventListener("click", (event) => {
        const tab = event.target.closest("[role='tab']");
        if (!tab) return;
        event.preventDefault();
        activateDashboardTab(tab, true);
    });

    // Support arrow keys plus Home and End for keyboard navigation between tabs.
    dashboardTabList?.addEventListener("keydown", (event) => {
        const currentIndex = dashboardTabs.indexOf(event.target.closest("[role='tab']"));
        if (currentIndex < 0) return;

        let nextIndex = currentIndex;
        if (event.key === "ArrowRight" || event.key === "ArrowDown") {
            nextIndex = (currentIndex + 1) % dashboardTabs.length;
        } else if (event.key === "ArrowLeft" || event.key === "ArrowUp") {
            nextIndex = (currentIndex - 1 + dashboardTabs.length) % dashboardTabs.length;
        } else if (event.key === "Home") {
            nextIndex = 0;
        } else if (event.key === "End") {
            nextIndex = dashboardTabs.length - 1;
        } else {
            return;
        }
        event.preventDefault();
        activateDashboardTab(dashboardTabs[nextIndex], true, true);
    });

    // Keep the visible panel synchronized with browser history and viewport changes.
    window.addEventListener("hashchange", activateTabFromLocation);
    window.addEventListener("popstate", activateTabFromLocation);
    window.addEventListener("resize", updateTabOrientation);
    updateTabOrientation();
    activateTabFromLocation();

    // Delegate clicks so buttons inserted during rendering can still change record status.
    document.addEventListener("click", (event) => {
        // Delegate clicks because venue and user action buttons are created during rendering.
        const venueButton = event.target.closest("[data-toggle-venue]");
        if (venueButton) {
            const venue = venues.find((item) => item.id === venueButton.dataset.toggleVenue);
            if (!venue) return;
            venue.active = venue.active === false;
            localStorage.setItem("courtOneVenues", JSON.stringify(venues));
            renderDashboard();
            return;
        }

        const userButton = event.target.closest("[data-toggle-user]");
        if (!userButton) return;
        const users = readList("courtOneUsers");
        const user = users.find((item) => item.id === userButton.dataset.toggleUser);
        if (!user) return;
        user.active = user.active === false;
        localStorage.setItem("courtOneUsers", JSON.stringify(users));
        renderDashboard();
    });

    renderDashboard();
})();