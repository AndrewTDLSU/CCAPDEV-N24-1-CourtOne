document.addEventListener("DOMContentLoaded", function() {
    
    const dropdown = document.getElementById("account-role");
    const popup = document.getElementById("business-name-group");

    // 1. Safety check: Verify the elements exist to avoid "null" errors
    if (!dropdown || !popup) {
        console.error("Could not find the dropdown or popup IDs. Check your HTML!");
        return;
    }

    // 2. Listen for the dropdown change
    dropdown.addEventListener("change", function() {
        // 'this.value' refers to the currently selected option's value
        if (this.value === "owner") {
            popup.style.display = "block";  // Show it
        } else {
            popup.style.display = "none";   // Hide it if they change their mind
        }
    });
});