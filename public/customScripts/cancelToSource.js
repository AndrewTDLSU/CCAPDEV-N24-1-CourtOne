// this script makes sure you return to the page you were previously on when you click a cancel button

document.addEventListener("DOMContentLoaded", () => { // run the script when html loads
    const cancelBtn = document.getElementById("cancelBtn"); // find cancel button
    if (!cancelBtn) {
        return;
    }
    
    const FALLBACK = "ownerDashboard.html"; // default destination
    const from = new URLSearchParams(window.location.search).get("from"); // returns the source via url bar

    // only allow pages on this same site, so ?from= can't send users elsewhere
    let backUrl = FALLBACK;
    if (from) {
        try {
            const url = new URL(from, window.location.href); // turns the source to a url
            if (url.origin === window.location.origin) { // check if page belongs to site
                backUrl = url.href; 
            }
        } catch {
            // invalid URL, keep the fallback
        }
    }

    cancelBtn.href = backUrl; // changes href url of the cancel button
});