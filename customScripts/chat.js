// past conversations
let chatHistory = {
    john: [
        { sender: "received", text: "Hello po!" },
        { sender: "sent", text: "Hi! Is Court 1 available?" },
        { sender: "received", text: "Yes, it is available!" }
    ],

    booking: [
        { sender: "received", text: "Welcome to CourtOne!" },
        { sender: "received", text: "How can we help you?" }
    ]
};

// selected conversation
let selectedConversation = "john";

// Display the messages
function displayMessages() {

    let messages = document.getElementById("messages");

    messages.innerHTML = "";

    chatHistory[selectedConversation].forEach(function(message) {

        let row = document.createElement("div");
        row.className = "chat-message " + message.sender;

        let bubble = document.createElement("div");
        bubble.className = "chat-bubble";
        bubble.textContent = message.text;

        let avatar = document.createElement("span");
        avatar.className = "chat-avatar";
        avatar.textContent = "👤";

        if (message.sender === "received") {
            row.appendChild(avatar);
            row.appendChild(bubble);
        } else {
            row.appendChild(bubble);
            row.appendChild(avatar);
        }

        messages.appendChild(row);
    });

    messages.scrollTop = messages.scrollHeight;
}

// Change conversations
document.querySelectorAll(".chat-contact").forEach(function(button) {

    button.addEventListener("click", function() {

        selectedConversation = button.dataset.contact;

        document.querySelectorAll(".chat-contact").forEach(function(contact) {
            contact.classList.remove("active");
        });

        button.classList.add("active");

        document.getElementById("chatTitle").textContent =
            selectedConversation === "john" ? "John Doe" : "Booking Staff";

        displayMessages();
    });
});

// Send a message
document.getElementById("chatForm").addEventListener("submit", function(event) {

    event.preventDefault();

    let input = document.getElementById("messageInput");
    let message = input.value.trim();

    if (message === "") {
        return;
    }

    chatHistory[selectedConversation].push({
        sender: "sent",
        text: message
    });

    displayMessages();

    input.value = "";
});

displayMessages();
