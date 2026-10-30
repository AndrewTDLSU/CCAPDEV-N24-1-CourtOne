
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("venue-search-form");
    const searchInput = document.getElementById("search");
    const venues = document.querySelectorAll("#venue-list .venue-column");
    const noResults = document.getElementById("no-results");

    function searchVenues() {
        const search = searchInput.value.trim().toLowerCase();
        const words = search.split(/\s+/).filter(Boolean);

        let count = 0;

        venues.forEach(function (venue) {
            const text = venue.dataset.search.toLowerCase();

            const matches = words.every(function (word) {
                return text.includes(word);
            });

            venue.classList.toggle("d-none", !matches);

            if (matches) {
                count++;
            }
        });

        noResults.classList.toggle("d-none", count > 0);
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        searchVenues();
    });

    searchInput.addEventListener("input", searchVenues);
});
