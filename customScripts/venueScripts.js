(() => {
    "use strict";

    // Shared seed venues keep viewVenue.html and venueForm.html consistent on first use.
    const sampleVenues = [
        { id: "pasay-sports-complex", name: "Pasay Sports Complex", sport: "Basketball", location: "GXWV+39H, Derham, Pasay City, Metro Manila", description: "Community sports courts in Pasay City.", courts: 4, price: 800, opens: "06:00", closes: "22:00", image: "media/venues/pasay-sports-complex.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "playground-cloverleaf", name: "The Playground by Cloverleaf", sport: "Multi-sport", location: "2499-2545 P. Zamora St, Manila, Metro Manila", description: "Flexible indoor sports venue near Cloverleaf.", courts: 3, price: 1000, opens: "07:00", closes: "23:00", image: "media/venues/playground-by-cloverleaf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "koliseum-ng-bayan", name: "Sports Complex (Koliseum ng Bayan)", sport: "Basketball", location: "H236+VHR, Washington, Makati City, Metro Manila", description: "Local sports complex with bookable courts.", courts: 2, price: 700, opens: "06:00", closes: "21:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking"], active: true },
        { id: "ph-arena", name: "PH Arena", sport: "Basketball", location: "Bulacan", description: "Basketball courts at the Philippine Arena sports venue.", courts: 4, price: 1200, opens: "06:00", closes: "22:00", image: "media/venues/koliseum-ng-bayan.jpg", amenities: ["Parking", "Restrooms"], active: true },
        { id: "gymnasium", name: "Gymnasium", sport: "Gymnastics", location: "Quezon City", description: "Indoor gymnastics facility with dedicated training space.", courts: 2, price: 900, opens: "07:00", closes: "21:00", image: "https://upload.wikimedia.org/wikipedia/commons/9/94/Shimonoseki_Gymnastics.jpg", imageCredit: "Photo by Bsx, CC0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:Shimonoseki_Gymnastics.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "araneta-coliseum", name: "Araneta Coliseum", sport: "Badminton", location: "Quezon City", description: "Indoor badminton courts in Quezon City.", courts: 4, price: 850, opens: "06:00", closes: "22:00", image: "https://upload.wikimedia.org/wikipedia/commons/1/1b/HK_SWCC_Sheung_Wan_Sports_Centre_12th_floor_interior_badmanton_courts_visitors_April_2017_IX1.jpg", imageCredit: "Photo by JIMZHEUNGWie, CC BY-SA 4.0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:HK_SWCC_Sheung_Wan_Sports_Centre_12th_floor_interior_badmanton_courts_visitors_April_2017_IX1.jpg", amenities: ["Changing rooms", "Restrooms"], active: true },
        { id: "sports-complex", name: "Sports Complex", sport: "Futsal", location: "Pasig", description: "Futsal courts with artificial turf in Pasig.", courts: 2, price: 1000, opens: "06:00", closes: "22:00", image: "https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg/960px-DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg", imageCredit: "Photo by PattayaPatrol, CC BY-SA 4.0, via Wikimedia Commons", imageCreditUrl: "https://commons.wikimedia.org/wiki/File:DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg", amenities: ["Changing rooms", "Restrooms"], active: true }
    ];

    // All demo pages exchange venue and booking records through localStorage.
    function readList(key, fallback = []) {
        return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
    }

    function getVenues() {
        const storedVenues = localStorage.getItem("courtOneVenues");
        const venues = readList("courtOneVenues", sampleVenues);
        const missingSamples = sampleVenues.filter((sample) =>
            !venues.some((venue) => venue.id === sample.id)
        );
        const futsalSample = sampleVenues.find((sample) => sample.id === "sports-complex");
        const existingFutsal = venues.find((venue) => venue.id === "sports-complex");
        const updatedFutsalImage = existingFutsal?.image ===
            "https://upload.wikimedia.org/wikipedia/commons/8/8b/DFC_5329_Evening_kickabout_in_Klet_Kaeo_-_teammates_line_up_as_a_player_strikes_the_ball_on_the_artificial_turf.jpg";
        if (updatedFutsalImage) {
            existingFutsal.image = futsalSample.image;
        }
        if (!storedVenues || missingSamples.length > 0 || updatedFutsalImage) {
            const mergedVenues = storedVenues ? [...venues, ...missingSamples] : venues;
            localStorage.setItem("courtOneVenues", JSON.stringify(mergedVenues));
            return mergedVenues;
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
        const params = new URLSearchParams(location.search);
        const legacyVenueId = params.get("venue");
        const venueId = params.get("id") || legacyVenueId || "pasay-sports-complex";
        const venue = venues.find((item) => item.id === venueId && item.active !== false);
        const notFound = document.getElementById("not-found");
        const reserveButton = document.getElementById("reserve-button");
        const reservationActions = document.querySelector(".reservation-actions");
        const summary = document.getElementById("booking-summary");
        const dateStrip = document.getElementById("date-strip");
        const tableHead = document.querySelector("#availability-table thead");
        const tableBody = document.querySelector("#availability-table tbody");
        const mobileDateList = document.getElementById("mobile-date-list");
        const datesToShow = 5;
        const mobileDatesToShow = 3;
        let dateOffset = 0;
        let selectedDate = "";
        let expandedDate = "";
        let selectedSlot = null;
        let completionMessage = "";
        let savedScrollY = 0;
        const pageStateKey = `courtOneVenuePageState:${venue.id}`;

        if (!venue) {
            content.hidden = true;
            notFound.hidden = false;
            return;
        }
        history.scrollRestoration = "manual";

        // Fill the responsive venue page with the selected, active venue record.
        document.title = `${venue.name} | CourtOne`;
        document.getElementById("venue-image").src = venue.image;
        document.getElementById("venue-image").alt = venue.name;
        const imageCredit = document.getElementById("venue-image-credit");
        if (venue.imageCredit && venue.imageCreditUrl) {
            const creditLink = document.createElement("a");
            creditLink.href = venue.imageCreditUrl;
            creditLink.target = "_blank";
            creditLink.rel = "noopener noreferrer";
            creditLink.textContent = venue.imageCredit;
            imageCredit.replaceChildren(creditLink);
            imageCredit.hidden = false;
        } else {
            imageCredit.hidden = true;
        }
        document.getElementById("venue-sport").textContent = `${venue.sport} Timeslots`;
        document.getElementById("venue-sport-meta").textContent = venue.sport;
        document.getElementById("venue-name").textContent = venue.name;
        document.getElementById("venue-location").textContent = venue.location;
        document.getElementById("venue-description").textContent = venue.description || "";
        document.getElementById("venue-courts").textContent =
            `${venue.courts} courts · ${(venue.amenities || []).join(", ") || "Venue facilities"}`;

        function dateAtOffset(offset) {
            const date = new Date();
            date.setHours(0, 0, 0, 0);
            date.setDate(date.getDate() + offset);
            return date;
        }

        function formatDate(date, options) {
            return date.toLocaleDateString("en-US", options);
        }

        function endTimeFor(time) {
            return `${String(Number(time.slice(0, 2)) + 1).padStart(2, "0")}:00`;
        }

        function getTimes() {
            const start = Number(venue.opens.split(":")[0]);
            const end = Number(venue.closes.split(":")[0]);
            const times = [];
            for (let hour = start; hour < end; hour += 1) {
                times.push(`${String(hour).padStart(2, "0")}:00`);
            }
            return times;
        }

        function getSlotState(date, court, time) {
            const bookings = readList("courtOneBookings");
            const endTime = `${String(Number(time.slice(0, 2)) + 1).padStart(2, "0")}:00`;
            const displayTime = `${formatTime(time)}–${formatTime(endTime)}`;
            const reserved = bookings.some((booking) =>
                booking.venueId === venue.id &&
                booking.date === date &&
                String(booking.court) === String(court) &&
                (booking.startTime === time || booking.time === displayTime)
            );
            const slotDate = new Date(`${date}T${time}:00`);
            const isPast = date === localDateString(new Date()) && slotDate <= new Date();
            return { available: !reserved && !isPast, reserved, displayTime };
        }

        function savePageState() {
            const pageState = {
                selectedDate,
                expandedDate,
                selectedSlot: selectedSlot && {
                    date: selectedSlot.date,
                    court: selectedSlot.court,
                    time: selectedSlot.time
                },
                completionMessage,
                scrollY: window.scrollY
            };
            sessionStorage.setItem(pageStateKey, JSON.stringify(pageState));
        }

        function restorePageState() {
            const saved = sessionStorage.getItem(pageStateKey);
            if (!saved) return;

            let pageState;
            try {
                pageState = JSON.parse(saved);
            } catch (error) {
                console.warn("Unable to restore the previous venue page state.", error);
                sessionStorage.removeItem(pageStateKey);
                return;
            }

            if (!pageState || typeof pageState !== "object") return;
            const today = dateAtOffset(0);
            const savedDate = new Date(`${pageState.selectedDate}T00:00:00`);
            if (Number.isFinite(savedDate.getTime()) && savedDate >= today) {
                selectedDate = localDateString(savedDate);
                const dateOffsetForSelection = Math.floor(
                    (Date.UTC(savedDate.getFullYear(), savedDate.getMonth(), savedDate.getDate()) -
                        Date.UTC(today.getFullYear(), today.getMonth(), today.getDate())) /
                    86400000
                );
                dateOffset = Math.floor(dateOffsetForSelection / datesToShow) * datesToShow;
            }

            const savedExpandedDate = new Date(`${pageState.expandedDate}T00:00:00`);
            if (Number.isFinite(savedExpandedDate.getTime()) && savedExpandedDate >= today) {
                expandedDate = localDateString(savedExpandedDate);
            }

            if (typeof pageState.completionMessage === "string") {
                completionMessage = pageState.completionMessage;
            }
            if (Number.isFinite(pageState.scrollY) && pageState.scrollY >= 0) {
                savedScrollY = pageState.scrollY;
            }

            const savedSlot = pageState.selectedSlot;
            if (
                savedSlot &&
                savedSlot.date === selectedDate &&
                getTimes().includes(savedSlot.time) &&
                /^\d+$/.test(savedSlot.court) &&
                Number(savedSlot.court) >= 1 &&
                Number(savedSlot.court) <= Number(venue.courts)
            ) {
                const state = getSlotState(savedSlot.date, savedSlot.court, savedSlot.time);
                if (state.available) {
                    selectedSlot = {
                        date: savedSlot.date,
                        court: savedSlot.court,
                        time: savedSlot.time,
                        displayTime: state.displayTime
                    };
                }
            }
        }

        function restoreScrollPosition() {
            const saved = sessionStorage.getItem(pageStateKey);
            if (!saved) return;

            try {
                const pageState = JSON.parse(saved);
                if (Number.isFinite(pageState.scrollY) && pageState.scrollY >= 0) {
                    window.setTimeout(() => window.scrollTo(0, pageState.scrollY), 50);
                }
            } catch (error) {
                console.warn("Unable to restore the previous venue scroll position.", error);
            }
        }

        function setSelectedSlot(slot) {
            selectedSlot = slot;
            completionMessage = "";
            reserveButton.disabled = !slot;
            reservationActions.hidden = !slot;
            summary.hidden = !slot;
            if (!slot) {
                summary.textContent = "";
                savePageState();
                return;
            }
            summary.textContent =
                `Court ${slot.court} · ${slot.date} · ${slot.displayTime} · ₱${Number(venue.price).toLocaleString("en-PH")}`;
            savePageState();
            renderSchedule();
        }

        function selectDate(date) {
            selectedDate = date;
            expandedDate = date;
            setSelectedSlot(null);
            savePageState();
            renderSchedule();
        }

        function makeDateButton(date, label) {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "date-choice";
            button.textContent = label;
            button.setAttribute("aria-pressed", String(date === selectedDate));
            button.addEventListener("click", () => selectDate(date));
            return button;
        }

        function createSlotButton(date, court, time, mobile = false) {
            const state = getSlotState(date, court, time);
            const button = document.createElement("button");
            const selected = selectedSlot &&
                selectedSlot.date === date &&
                selectedSlot.court === String(court) &&
                selectedSlot.time === time;
            button.type = "button";
            button.className = mobile
                ? `mobile-slot${selected ? " selected" : ""}`
                : `slot-cell ${state.available ? "available" : "booked"}${selected ? " selected" : ""}`;
            button.textContent = mobile
                ? `${formatTime(time)} · Court ${court}`
                : state.available ? "Available" : "Booked";
            button.disabled = !state.available;
            button.setAttribute("aria-pressed", String(Boolean(selected)));
            if (state.reserved) button.title = "This slot has already been reserved.";
            if (state.available) {
                button.addEventListener("click", () => {
                    setSelectedSlot({
                        date,
                        court: String(court),
                        time,
                        displayTime: state.displayTime
                    });
                });
            }
            return button;
        }

        function renderDateStrip() {
            dateStrip.replaceChildren();
            for (let index = 0; index < datesToShow; index += 1) {
                const date = dateAtOffset(dateOffset + index);
                const dateValue = localDateString(date);
                dateStrip.append(makeDateButton(dateValue, formatDate(date, { month: "short", day: "numeric" })));
            }
        }

        function renderDesktopTable() {
            const headRow = document.createElement("tr");
            const timeHeader = document.createElement("th");
            timeHeader.scope = "col";
            timeHeader.textContent = "Time";
            headRow.append(timeHeader);
            for (let court = 1; court <= Number(venue.courts); court += 1) {
                const header = document.createElement("th");
                header.scope = "col";
                header.textContent = `Court ${court}`;
                headRow.append(header);
            }
            tableHead.replaceChildren(headRow);

            const rows = getTimes().map((time) => {
                const row = document.createElement("tr");
                const timeHeaderCell = document.createElement("th");
                timeHeaderCell.scope = "row";
                timeHeaderCell.textContent = `${formatTime(time)}–${formatTime(endTimeFor(time))}`;
                row.append(timeHeaderCell);
                for (let court = 1; court <= Number(venue.courts); court += 1) {
                    const cell = document.createElement("td");
                    cell.append(createSlotButton(selectedDate, court, time));
                    row.append(cell);
                }
                return row;
            });
            tableBody.replaceChildren(...rows);
        }

        function renderMobileCards() {
            mobileDateList.replaceChildren();
            for (let index = 0; index < mobileDatesToShow; index += 1) {
                const date = dateAtOffset(index);
                const dateValue = localDateString(date);
                const card = document.createElement("article");
                const row = document.createElement("div");
                const calendar = document.createElement("div");
                const copy = document.createElement("div");
                const title = document.createElement("h3");
                const times = document.createElement("p");
                const action = document.createElement("div");
                const toggle = document.createElement("button");
                const slotList = document.createElement("div");
                const availableTimes = getTimes().filter((time) =>
                    Array.from({ length: Number(venue.courts) }, (_, courtIndex) =>
                        getSlotState(dateValue, courtIndex + 1, time).available
                    ).some(Boolean)
                );
                const firstAvailable = availableTimes[0];
                const lastAvailable = availableTimes[availableTimes.length - 1];

                card.className = `mobile-date-card${dateValue === expandedDate ? " expanded" : ""}`;
                row.className = "mobile-date-row";
                calendar.className = "calendar-icon";
                calendar.setAttribute("aria-hidden", "true");
                calendar.textContent = "▦";
                copy.className = "mobile-date-copy";
                title.textContent = formatDate(date, { month: "long", day: "numeric" });
                times.textContent = firstAvailable
                    ? `${formatTime(firstAvailable)} to ${formatTime(endTimeFor(lastAvailable))}`
                    : "No times available";
                copy.append(title, times);
                action.className = "mobile-date-action";
                toggle.type = "button";
                toggle.setAttribute("aria-expanded", String(dateValue === expandedDate));
                toggle.setAttribute("aria-label", `View courts for ${title.textContent}`);
                toggle.className = dateValue === expandedDate ? "mobile-date-toggle is-open" : "mobile-date-toggle";
                toggle.textContent = dateValue === expandedDate ? "⌄" : "›";
                toggle.addEventListener("click", () => {
                    if (dateValue === selectedDate && dateValue === expandedDate) {
                        expandedDate = "";
                        savePageState();
                        renderSchedule();
                    } else {
                        selectDate(dateValue);
                    }
                });
                action.append(toggle);
                if (index === 0) {
                    const viewButton = document.createElement("button");
                    const hint = document.createElement("small");
                    viewButton.type = "button";
                    viewButton.textContent = "View Court";
                    hint.textContent = "Tap to view court";
                    viewButton.addEventListener("click", () => selectDate(dateValue));
                    action.replaceChildren(viewButton, hint);
                }
                row.append(calendar, copy, action);
                slotList.className = "mobile-slot-list";
                for (const time of getTimes()) {
                    for (let court = 1; court <= Number(venue.courts); court += 1) {
                        slotList.append(createSlotButton(dateValue, court, time, true));
                    }
                }
                card.append(row, slotList);
                mobileDateList.append(card);
            }
        }

        function renderSchedule() {
            renderDateStrip();
            renderDesktopTable();
            renderMobileCards();
        }

        document.getElementById("date-previous").addEventListener("click", () => {
            dateOffset = Math.max(0, dateOffset - datesToShow);
            savePageState();
            const visibleDate = localDateString(dateAtOffset(dateOffset));
            if (selectedDate < visibleDate) selectDate(visibleDate);
            else renderSchedule();
        });
        document.getElementById("date-next").addEventListener("click", () => {
            dateOffset += datesToShow;
            savePageState();
            selectDate(localDateString(dateAtOffset(dateOffset)));
        });
        reserveButton.addEventListener("click", () => {
            if (!selectedSlot) return;
            // Store a demo reservation locally; no payment service or server is contacted.
            const bookings = readList("courtOneBookings");
            const booking = {
                id: `booking-${Date.now()}`,
                venueId: venue.id,
                venueName: venue.name,
                date: selectedSlot.date,
                court: selectedSlot.court,
                startTime: selectedSlot.time,
                time: selectedSlot.displayTime,
                price: Number(venue.price),
                status: "Demo reservation"
            };
            bookings.push(booking);
            localStorage.setItem("courtOneBookings", JSON.stringify(bookings));
            selectedSlot = null;
            completionMessage = `Demo reservation saved for ${booking.venueName} on ${booking.date}, Court ${booking.court} at ${booking.time}. Payment processing is not enabled.`;
            reserveButton.disabled = true;
            summary.textContent = completionMessage;
            summary.hidden = false;
            reservationActions.hidden = false;
            savePageState();
            renderSchedule();
            window.location.href = "payment.html";
        });

        selectedDate = localDateString(dateAtOffset(0));
        restorePageState();
        reserveButton.disabled = !selectedSlot;
        reservationActions.hidden = !selectedSlot && !completionMessage;
        summary.hidden = !selectedSlot && !completionMessage;
        summary.textContent = selectedSlot
            ? `Court ${selectedSlot.court} · ${selectedSlot.date} · ${selectedSlot.displayTime} · ₱${Number(venue.price).toLocaleString("en-PH")}`
            : completionMessage;
        renderSchedule();
        window.addEventListener("pagehide", savePageState);
        window.addEventListener("pageshow", restoreScrollPosition);
        if (savedScrollY > 0) {
            window.setTimeout(() => window.scrollTo(0, savedScrollY), 50);
        }
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