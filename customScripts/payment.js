
function toggleCard() {

    let isCard = document.getElementById("card").checked;

    document.getElementById("cardFields").hidden = !isCard;

    document.getElementById("cardNumber").required = isCard;
    document.getElementById("cvn").required = isCard;
    document.getElementById("expiry").required = isCard;
}

function payNow(event) {

    event.preventDefault();

    let selected = document.querySelector('input[name="payment"]:checked');

    let message = document.getElementById("message");

    if (selected === null) {
        message.textContent = "Please select a payment method.";
        message.style.color = "red";
        return;
    }

    message.textContent = "Demo checkout complete. No payment was made.";
    message.style.color = "green";
}
