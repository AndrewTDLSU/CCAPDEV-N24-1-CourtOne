/**
 * Asynchronous Support Feed Operations Layout Handler Script
 * Intercepts form submit vectors to display inputs without causing systemic page reloads
 */
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("msgPostFormAction");
    const stream = document.getElementById("msgStreamContainer");

    if (form && stream) {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Securely checks form execution loop blocks
            
            const payload = new FormData(form);
            const alias = payload.get("sender_name");
            const content = payload.get("message");

            // Construct new message row UI bubble container dynamically
            const segment = document.createElement("div");
            segment.className = "p-2 border rounded bg-white mb-2 small text-dark shadow-xs animate-fade";
            segment.innerHTML = `<strong>${escapeHtml(alias)}:</strong> ${escapeHtml(content)}`;
            
            stream.appendChild(segment);
            stream.scrollTop = stream.scrollHeight; // Force scroll to terminal limits

            // Dispatch payload asynchronously out to our background MVC controller endpoint
            fetch(form.action, { method: "POST", body: payload });
            form.querySelector('input[name="message"]').value = ""; // Empty string message input container
        });
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }
});
