(() => {
    "use strict";

    // Shared seed venues keep viewVenue.html and venueForm.html consistent on first use.
    const sampleVenues = [
        { id: "pasay-sports-complex", name: "Pasay Sports Complex", sport: "Basketball", location: "GXWV+39H, Derham, Pasay City, Metro Manila", description: "Community sports courts in Pasay City.", courts: 4, price: 800, opens: "06:00", closes: "22:00", image: "media/venues/pasay-sports-complex.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "playground-cloverleaf", name: "The Playground by Cloverleaf", sport: "Multi-sport", location: "2499-2545 P. Zamora St, Manila, Metro Manila", description: "Flexible indoor sports venue near Cloverleaf.", courts: 3, price: 1000, opens: "07:00", closes: "23:00", image: "media/venues/playground-by-cloverleaf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "koliseum-ng-bayan", name: "Sports Complex (Koliseum ng Bayan)", sport: "Basketball", location: "H236+VHR, Washington, Makati City, Metro Manila", description: "Local sports complex with bookable courts.", courts: 2, price: 700, opens: "06:00", closes: "21:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking"], active: true }
    ];

    // All demo pages exchange venue and booking records through localStorage.
    function readList(key, fallback = []) {
        return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
    }

    function getVenues() {
        const venues = readList("courtOneVenues", sampleVenues);
        if (!localStorage.getItem("courtOneVenues")) {
            localStorage.setItem("courtOneVenues", JSON.stringify(venues));
        }
        return venues;
    }

    function localDateString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    function formatTime(time) {
        return new Date(`2000-01-01T${time}:00`).toLocaleTimeString([], {
            hour: "numeric",
            minute: "2-digit"
        });
    }

    function initializeVenueDetails() {
        const content = document.getElementById("venue-content");
        // venueScripts.js is also loaded by venueForm.html, which has no venue detail panel.
        if (!content) return;

        const venues = getVenues();
        const venueId = new URLSearchParams(location.search).get("id") || "pasay-sports-complex";
        const venue = venues.find((item) => item.id === venueId && item.active !== false);
        const notFound = document.getElementById("not-found");
        const slotList = document.getElementById("slot-list");
        const dateInput = document.getElementById("booking-date");
        const courtChoice = document.getElementById("court-choice");
        const reserveButton = document.getElementById("reserve-button");
        const summary = document.getElementById("booking-summary");
        let selectedTime = "";

        if (!venue) {
            content.classList.add("d-none");
            notFound.classList.remove("d-none");
            return;
        }

        // Fill the static page shell with the selected, active venue record.
        document.title = `${venue.name} | CourtOne`;
        document.getElementById("venue-image").src = venue.image;
        document.getElementById("venue-image").alt = venue.name;
        document.getElementById("venue-sport").textContent = venue.sport;
        document.getElementById("venue-name").textContent = venue.name;
        document.getElementById("venue-location").textContent = venue.location;
        document.getElementById("venue-description").textContent = venue.description || "Venue information provided by the owner.";
        document.getElementById("venue-courts").textContent = venue.courts;
        document.getElementById("venue-hours").textContent = `${formatTime(venue.opens)} – ${formatTime(venue.closes)}`;
        document.getElementById("venue-amenities").textContent = (venue.amenities || []).join(", ") || "Not specified";
        document.getElementById("venue-price").textContent = `₱${Number(venue.price).toLocaleString("en-PH")}`;
        dateInput.min = localDateString(new Date());
        dateInput.value = dateInput.min;
        for (let court = 1; court <= Number(venue.courts); court += 1) {
            const option = document.createElement("option");
            option.value = String(court);
            option.textContent = `Court ${court}`;
            courtChoice.append(option);
        }

        function updateSlots() {
            selectedTime = "";
            reserveButton.disabled = true;
            summary.classList.add("d-none");
            slotList.replaceChildren();
            const start = Number(venue.opens.split(":")[0]);
            const end = Number(venue.closes.split(":")[0]);
            const isToday = dateInput.value === localDateString(new Date());
            // Past time slots and matching reservations are disabled for this court and date.
            const bookings = readList("courtOneBookings");
            for (let hour = start; hour < end; hour += 1) {
                const time = `${String(hour).padStart(2, "0")}:00`;
                const slotDate = new Date(`${dateInput.value}T${time}:00`);
                const isPast = isToday && slotDate <= new Date();
                const displayTime = `${formatTime(time)}–${formatTime(`${String(hour + 1).padStart(2, "0")}:00`)}`;
                const isReserved = bookings.some((booking) =>
                    booking.venueId === venue.id &&
                    booking.date === dateInput.value &&
                    String(booking.court) === courtChoice.value &&
                    (booking.startTime === time || booking.time === displayTime)
                );
                const button = document.createElement("button");
                button.type = "button";
                button.className = "btn btn-outline-primary slot-button";
                button.textContent = formatTime(time);
                button.disabled = isPast || isReserved;
                if (isReserved) button.title = "This slot has already been reserved.";
                button.setAttribute("aria-pressed", "false");
                button.addEventListener("click", () => {
                    selectedTime = time;
                    for (const slot of slotList.querySelectorAll("button")) {
                        slot.classList.remove("active");
                        slot.setAttribute("aria-pressed", "false");
                    }
                    button.classList.add("active");
                    button.setAttribute("aria-pressed", "true");
                    reserveButton.disabled = false;
                    summary.textContent = `Court ${courtChoice.value} · ${dateInput.value} · ${displayTime} · ₱${Number(venue.price).toLocaleString("en-PH")}`;
                    summary.classList.remove("d-none");
                });
                slotList.append(button);
            }
            const available = [...slotList.querySelectorAll("button")].some((button) => !button.disabled);
            document.getElementById("no-slots").classList.toggle("d-none", available);
        }

        dateInput.addEventListener("change", updateSlots);
        courtChoice.addEventListener("change", updateSlots);
        reserveButton.addEventListener("click", () => {
            if (!selectedTime || !dateInput.value) return;
            // Store a demo reservation locally; no payment service or server is contacted.
            const bookings = readList("courtOneBookings");
            const booking = {
                id: `booking-${Date.now()}`,
                venueId: venue.id,
                venueName: venue.name,
                date: dateInput.value,
                court: courtChoice.value,
                startTime: selectedTime,
                time: `${formatTime(selectedTime)}–${formatTime(`${String(Number(selectedTime.slice(0, 2)) + 1).padStart(2, "0")}:00`)}`,
                price: Number(venue.price),
                status: "Demo reservation"
            };
            bookings.push(booking);
            localStorage.setItem("courtOneBookings", JSON.stringify(bookings));
            selectedTime = "";
            updateSlots();
            summary.className = "alert alert-success";
            summary.textContent = `Demo reservation saved for ${booking.venueName} on ${booking.date}, Court ${booking.court} at ${booking.time}. Payment processing is not enabled.`;
        });
        updateSlots();
    }

    function initializeVenueForm() {
        const venueForm = document.getElementById("venue-form");
        // Skip form setup while this file is running on viewVenue.html.
        if (!venueForm) return;

        const formMessage = document.getElementById("form-message");
        const photoInput = document.getElementById("venue-photo");
        const photoPreview = document.getElementById("photo-preview");
        const deleteButton = document.getElementById("delete-venue");
        const requestedId = new URLSearchParams(location.search).get("id");
        let venues = getVenues();
        let existingVenue = venues.find((venue) => venue.id === requestedId);
        let selectedImage = "";
        const fields = {
            name: document.getElementById("venue-name"),
            sport: document.getElementById("sport-choice"),
            location: document.getElementById("venue-location"),
            description: document.getElementById("venue-description"),
            courts: document.getElementById("court-count"),
            price: document.getElementById("price-per-hour"),
            active: document.getElementById("venue-status"),
            opens: document.getElementById("opens-at"),
            closes: document.getElementById("closes-at")
        };

        function showMessage(message, type) {
            formMessage.className = `alert alert-${type}`;
            formMessage.textContent = message;
        }

        function populateForm(venue) {
            if (!venue) return;
            // A matching query-string ID selects edit mode; without one the form adds a venue.
            document.getElementById("form-title").textContent = "Edit venue";
            document.title = `Edit ${venue.name} | CourtOne`;
            for (const key of Object.keys(fields)) {
                fields[key].value = venue[key] ?? "";
            }
            for (const checkbox of document.querySelectorAll(".amenity")) {
                checkbox.checked = (venue.amenities || []).includes(checkbox.value);
            }
            photoPreview.src = venue.image || "media/icons/sports.png";
            photoPreview.alt = `${venue.name} photo preview`;
            deleteButton.classList.remove("d-none");
        }

        populateForm(existingVenue);

        photoInput.addEventListener("change", () => {
            const file = photoInput.files[0];
            if (!file) return;
            if (!file.type.startsWith("image/")) {
                photoInput.setCustomValidity("Choose an image file.");
                photoInput.reportValidity();
                photoInput.setCustomValidity("");
                photoInput.value = "";
                return;
            }
            if (file.size > 750 * 1024) {
                photoInput.setCustomValidity("Choose an image smaller than 750 KB.");
                photoInput.reportValidity();
                photoInput.setCustomValidity("");
                photoInput.value = "";
                return;
            }
            const reader = new FileReader();
            reader.addEventListener("load", () => {
                // Keep the selected image as a data URL so the preview remains available after saving.
                selectedImage = String(reader.result);
                photoPreview.src = selectedImage;
            });
            reader.readAsDataURL(file);
        });

        venueForm.addEventListener("submit", (event) => {
            event.preventDefault();
            if (!venueForm.reportValidity()) return;
            if (fields.closes.value <= fields.opens.value) {
                showMessage("Closing time must be later than opening time.", "warning");
                fields.closes.focus();
                return;
            }

            // Reload before writing so the form uses the current list from browser storage.
            venues = getVenues();
            existingVenue = venues.find((venue) => venue.id === requestedId);
            const venue = {
                ...(existingVenue || {}),
                id: existingVenue ? existingVenue.id : `venue-${Date.now()}`,
                name: fields.name.value.trim(),
                sport: fields.sport.value,
                location: fields.location.value.trim(),
                description: fields.description.value.trim(),
                courts: Number(fields.courts.value),
                price: Number(fields.price.value),
                opens: fields.opens.value,
                closes: fields.closes.value,
                image: selectedImage || (existingVenue && existingVenue.image) || "media/icons/sports.png",
                amenities: [...document.querySelectorAll(".amenity:checked")].map((checkbox) => checkbox.value),
                active: fields.active.value === "true",
                updatedAt: new Date().toISOString()
            };
            if (existingVenue) {
                venues = venues.map((item) => item.id === venue.id ? venue : item);
            } else {
                venues.push(venue);
            }
            localStorage.setItem("courtOneVenues", JSON.stringify(venues));
            showMessage(`“${venue.name}” was saved in this browser.`, "success");
            if (!requestedId) {
                venueForm.reset();
                photoPreview.src = "media/icons/sports.png";
                selectedImage = "";
            }
        });

        deleteButton.addEventListener("click", () => {
            if (!existingVenue || !window.confirm(`Delete “${existingVenue.name}” from this browser?`)) return;
            venues = getVenues().filter((venue) => venue.id !== existingVenue.id);
            localStorage.setItem("courtOneVenues", JSON.stringify(venues));
            location.href = "adminDashboard.html#venues";
        });
    }

    initializeVenueDetails();
    initializeVenueForm();
})();