
let searchInput = document.getElementById("searchInput");
let sportFilter = document.getElementById("sportFilter");
let venues = document.querySelectorAll(".bv-card");

function filterVenues() {

    let search = searchInput.value.toLowerCase().trim();
    let sport = sportFilter.value;
    let count = 0;

    venues.forEach(function(venue) {

        let venueText = venue.textContent.toLowerCase();
        let venueSport = venue.dataset.sport;

        let matchesSearch = venueText.includes(search);
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

searchInput.addEventListener("input", filterVenues);

sportFilter.addEventListener("change", filterVenues);

document.getElementById("clearFilters").addEventListener("click", function() {

    searchInput.value = "";
    sportFilter.value = "all";

    filterVenues();
});


filterVenues();
