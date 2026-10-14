(() => {
    "use strict";

    // Seed data lets the admin page demonstrate venue management before any venues are added.
    const sampleVenues = [
        { id: "pasay-sports-complex", name: "Pasay Sports Complex", sport: "Basketball", location: "GXWV+39H, Derham, Pasay City, Metro Manila", description: "Community sports courts in Pasay City.", courts: 4, price: 800, opens: "06:00", closes: "22:00", image: "media/venues/pasay-sports-complex.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "playground-cloverleaf", name: "The Playground by Cloverleaf", sport: "Multi-sport", location: "2499-2545 P. Zamora St, Manila, Metro Manila", description: "Flexible indoor sports venue near Cloverleaf.", courts: 3, price: 1000, opens: "07:00", closes: "23:00", image: "media/venues/playground-by-cloverleaf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "koliseum-ng-bayan", name: "Sports Complex (Koliseum ng Bayan)", sport: "Basketball", location: "H236+VHR, Washington, Makati City, Metro Manila", description: "Local sports complex with bookable courts.", courts: 2, price: 700, opens: "06:00", closes: "21:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking"], active: true }
    ];

    // Read JSON-backed demo records, using the fallback only when the key is absent.
    function readList(key, fallback = []) {
        return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
    }

    // Escape values inserted into table markup so stored user-entered text stays text.
    function escapeHtml(value) {
        return String(value ?? "").replace(/[&<>"']/g, (character) => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
        })[character]);
    }

    const venueRows = document.getElementById("venue-rows");
    // This script is shared by the site but should act only on the admin dashboard.
    if (!venueRows) return;

    let venues = readList("courtOneVenues", sampleVenues);
    // Persist the seed list once so venue form and venue details pages share the same records.
    if (!localStorage.getItem("courtOneVenues")) {
        localStorage.setItem("courtOneVenues", JSON.stringify(venues));
    }

    function renderDashboard() {
        const users = readList("courtOneUsers");
        const bookings = readList("courtOneBookings");
        const activeVenues = venues.filter((venue) => venue.active !== false);
        document.getElementById("user-count").textContent = users.length;
        document.getElementById("owner-count").textContent = users.filter((user) => user.role === "owner").length;
        document.getElementById("venue-count").textContent = activeVenues.length;
        document.getElementById("booking-count").textContent = bookings.length;

        // Rebuild the dashboard summaries and tables from the latest browser-stored records.
        venueRows.innerHTML = venues.map((venue) => `
            <tr>
                <td><div class="d-flex align-items-center gap-2"><img class="venue-thumb rounded" src="${escapeHtml(venue.image)}" alt=""><span class="fw-semibold">${escapeHtml(venue.name)}</span></div></td>
                <td>${escapeHtml(venue.sport)}</td><td>${escapeHtml(venue.location)}</td>
                <td><span class="badge ${venue.active === false ? "text-bg-secondary" : "text-bg-success"}">${venue.active === false ? "Unlisted" : "Listed"}</span></td>
                <td><div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-primary" href="viewVenue.html?id=${encodeURIComponent(venue.id)}">View</a><a class="btn btn-sm btn-outline-secondary" href="venueForm.html?id=${encodeURIComponent(venue.id)}">Edit</a><button class="btn btn-sm btn-outline-dark" type="button" data-toggle-venue="${escapeHtml(venue.id)}">${venue.active === false ? "Restore" : "Unlist"}</button></div></td>
            </tr>`).join("");
        document.getElementById("empty-venues").classList.toggle("d-none", venues.length > 0);

        const userRows = document.getElementById("user-rows");
        userRows.innerHTML = users.map((user) => `
            <tr><td><span class="fw-semibold">${escapeHtml(user.name)}</span><br><small class="text-secondary">${escapeHtml(user.email)}</small></td>
                <td>${user.role === "owner" ? "Venue owner" : "Customer"}</td>
                <td><span class="badge ${user.active === false ? "text-bg-secondary" : "text-bg-success"}">${user.active === false ? "Inactive" : "Active"}</span></td>
                <td><button class="btn btn-sm btn-outline-dark" type="button" data-toggle-user="${escapeHtml(user.id)}">${user.active === false ? "Activate" : "Deactivate"}</button></td></tr>`).join("");
        document.getElementById("empty-users").classList.toggle("d-none", users.length > 0);

        const bookingRows = document.getElementById("booking-rows");
        bookingRows.innerHTML = bookings.slice().reverse().slice(0, 8).map((booking) => `
            <tr><td>${escapeHtml(booking.venueName)}</td><td>${escapeHtml(booking.date)}</td><td>Court ${escapeHtml(booking.court)} · ${escapeHtml(booking.time)}</td></tr>`).join("");
        document.getElementById("empty-bookings").classList.toggle("d-none", bookings.length > 0);
    }

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