/* ==========================================================================
   COURT ARENA PLATFORM SEPARATE PRODUCTION JS MODULE
   ========================================================================== */
document.addEventListener("DOMContentLoaded", function() {
    console.log("[System Core] Separate script module loaded cleanly.");
    const chatForm = document.getElementById("msgPostFormAction");
    const msgStream = document.getElementById("msgStreamContainer");
    if (chatForm && msgStream) {
        chatForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const data = new FormData(chatForm);
            const item = document.createElement("div");
            item.className = "p-2 border rounded bg-white mb-2 small text-dark";
            item.innerHTML = `<strong>${data.get("sender_name")}:</strong> ${data.get("message")}`;
            msgStream.appendChild(item);
            msgStream.scrollTop = msgStream.scrollHeight;
            fetch(chatForm.action, { method: "POST", body: data });
            chatForm.querySelector('input[name="message"]').value = "";
        });
    }
});