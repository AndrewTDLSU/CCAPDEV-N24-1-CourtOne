function filterVenues() {

    let search = document.getElementById("searchInput").value.toLowerCase();

    let sport = document.getElementById("sportFilter").value;

    let venues = document.querySelectorAll(".bv-card");

    let count = 0;

    venues.forEach(function(venue) {

        let text = venue.textContent.toLowerCase();

        let venueSport = venue.dataset.sport;

        let matchesSearch = text.includes(search);

        let matchesSport = sport === "all" || sport === venueSport;

        if (matchesSearch && matchesSport) {
            venue.hidden = false;
            count++;
        } else {
            venue.hidden = true;
        }

    });

    document.getElementById("resultCount").textContent =
        count + (count === 1 ? " venue found" : " venues found");

    document.getElementById("noResults").hidden = count !== 0;
}
