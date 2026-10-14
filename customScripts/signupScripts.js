(() => {
    "use strict";

    // This file is loaded only by signup.html; stop if the form is not present.
    const signupForm = document.getElementById("signup-form");
    if (!signupForm) return;

    const roleSelect = document.getElementById("account-role");
    const businessGroup = document.getElementById("business-name-group");
    const businessInput = document.getElementById("business-name");
    const passwordInput = document.getElementById("password");
    const confirmInput = document.getElementById("confirm-password");
    const signupMessage = document.getElementById("signup-message");

    // Venue owners must provide a business name; customers do not need this field.
    function updateRoleFields() {
        const isOwner = roleSelect.value === "owner";
        businessGroup.classList.toggle("d-none", !isOwner);
        businessInput.disabled = !isOwner;
        businessInput.required = isOwner;
    }

    // Use the browser's native form validation message for password mismatches.
    function validatePasswords() {
        confirmInput.setCustomValidity(
            confirmInput.value && confirmInput.value !== passwordInput.value
                ? "Passwords do not match."
                : ""
        );
    }

    roleSelect.addEventListener("change", updateRoleFields);
    passwordInput.addEventListener("input", validatePasswords);
    confirmInput.addEventListener("input", validatePasswords);
    updateRoleFields();

    signupForm.addEventListener("submit", (event) => {
        event.preventDefault();
        validatePasswords();
        if (!signupForm.reportValidity()) return;

        // Save only public demo profile details; never persist either password field.
        const formData = new FormData(signupForm);
        const email = String(formData.get("email")).trim().toLowerCase();
        const users = JSON.parse(localStorage.getItem("courtOneUsers") || "[]");
        if (users.some((user) => user.email === email)) {
            signupMessage.className = "alert alert-warning";
            signupMessage.textContent = "A demo profile with this email already exists in this browser.";
            return;
        }

        users.push({
            id: `user-${Date.now()}`,
            name: String(formData.get("name")).trim(),
            email,
            role: String(formData.get("role")),
            businessName: String(formData.get("businessName") || "").trim(),
            active: true,
            createdAt: new Date().toISOString()
        });
        localStorage.setItem("courtOneUsers", JSON.stringify(users));
        signupForm.reset();
        updateRoleFields();
        signupMessage.className = "alert alert-success";
        signupMessage.textContent = "Demo profile created on this device. Sign-in and authentication are not connected in this prototype.";
    });
})();