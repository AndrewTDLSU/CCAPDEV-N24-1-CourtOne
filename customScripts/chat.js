
let currentChat = "john";

function openChat(name) {

    document.getElementById(currentChat).hidden = true;

    document.getElementById(name).hidden = false;

    currentChat = name;

    document.getElementById("chatTitle").textContent =
        name === "john" ? "John Doe" : "Booking Staff";
}

function sendMessage() {

    let input = document.getElementById("messageInput");

    let message = input.value.trim();

    if (message === "") {
        return;
    }

    let bubble = document.createElement("p");

    bubble.className = "sent";

    bubble.textContent = message;

    let chat = document.getElementById(currentChat);

    chat.appendChild(bubble);

    chat.scrollTop = chat.scrollHeight;

    input.value = "";
}
