
function filterVenues() {

    let search = document.getElementById("searchInput").value.toLowerCase();

    let sport = document.getElementById("sportFilter").value;

    let venues = document.querySelectorAll(".venue");

    let count = 0;

    venues.forEach(function(venue) {

        let matchesSearch = venue.textContent.toLowerCase().includes(search);

        let matchesSport = sport === "all" || venue.dataset.sport === sport;

        if (matchesSearch && matchesSport) {
            venue.hidden = false;
            count++;
        } else {
            venue.hidden = true;
        }

    });

    document.getElementById("count").textContent = count + " venues found";
}

