
let paymentOptions = document.querySelectorAll('input[name="payment"]');

paymentOptions.forEach(function(option) {
    option.addEventListener("change", function() {

        let isCard = option.value === "Card";

        document.getElementById("cardFields").hidden = !isCard;

        document.getElementById("cardNumber").required = isCard;
        document.getElementById("cvn").required = isCard;
        document.getElementById("expiry").required = isCard;
    });
});

document.getElementById("paymentForm").addEventListener("submit", function(event) {

    event.preventDefault();

    let selected = document.querySelector('input[name="payment"]:checked');

    if (selected === null) {
        document.getElementById("paymentMessage").textContent =
            "Please select a payment method.";
        return;
    }

    document.getElementById("paymentMessage").textContent =
        "Demo booking confirmed! No actual payment was made.";

    document.getElementById("paymentMessage").style.color = "green";

});
