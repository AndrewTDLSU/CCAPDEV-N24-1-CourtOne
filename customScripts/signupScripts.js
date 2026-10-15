(() => {
    // Keep form variables and handlers private to this page.
    "use strict";

    // Show the requested account form when its inline prompt is clicked.
    function showAccountPanel(panelId) {
        document.querySelectorAll(".auth-panel").forEach((panel) => {
            panel.hidden = panel.id !== panelId;
        });
    }

    // Keep account switching available without displaying tabs below the brand.
    document.querySelectorAll("[data-show-panel]").forEach((switchButton) => {
        switchButton.addEventListener("click", () => {
            showAccountPanel(switchButton.dataset.showPanel);
        });
    });

    // Toggle the compact navigation on narrow screens and expose its expanded state.
    const menuButton = document.querySelector(".menu-button");
    const mainNavigation = document.getElementById("main-navigation");
    if (menuButton && mainNavigation) {
        menuButton.addEventListener("click", () => {
            const isOpen = mainNavigation.classList.toggle("is-open");
            menuButton.setAttribute("aria-expanded", String(isOpen));
        });
    }

    // Display an explicit notice because prototype sign-in is not connected to authentication.
    const loginForm = document.getElementById("login-form");
    const loginMessage = document.getElementById("login-message");
    if (loginForm && loginMessage) {
        loginForm.addEventListener("submit", (event) => {
            event.preventDefault();
            loginMessage.className = "auth-message alert-info";
            loginMessage.textContent = "Sign-in and authentication are not connected in this prototype.";
            loginMessage.hidden = false;
        });
    }

    // Find the registration form before wiring its validation and local save behavior.
    const signupForm = document.getElementById("signup-form");
    if (!signupForm) return;

    // Cache fields used to show role-specific inputs and validate credentials.
    const roleSelect = document.getElementById("account-role");
    const businessGroup = document.getElementById("business-name-group");
    const businessInput = document.getElementById("business-name");
    const passwordInput = document.getElementById("password");
    const confirmInput = document.getElementById("confirm-password");
    const signupMessage = document.getElementById("signup-message");

    // Venue owners must provide a business name; customers do not need this field.
    function updateRoleFields() {
        // Determine which account type is currently selected.
        const isOwner = roleSelect.value === "owner";
        // Show the business field only for venue owners.
        businessGroup.classList.toggle("d-none", !isOwner);
        // Disable and require the field in step with its visibility.
        businessInput.disabled = !isOwner;
        businessInput.required = isOwner;
    }

    // Use the browser's native form validation message for password mismatches.
    function validatePasswords() {
        // An empty confirmation is handled by required validation; otherwise compare both values.
        confirmInput.setCustomValidity(
            confirmInput.value && confirmInput.value !== passwordInput.value
                ? "Passwords do not match."
                : ""
        );
    }

    // Refresh role-dependent fields whenever the selected account type changes.
    roleSelect.addEventListener("change", updateRoleFields);
    // Recheck the password pair while either password is being edited.
    passwordInput.addEventListener("input", validatePasswords);
    confirmInput.addEventListener("input", validatePasswords);
    // Initialize the conditional business-name field for the default role.
    updateRoleFields();

    // Validate and save the demo profile when the form is submitted.
    signupForm.addEventListener("submit", (event) => {
        // Keep submission on this page so the profile can be handled locally.
        event.preventDefault();
        // Update mismatch feedback before asking the browser to validate the form.
        validatePasswords();
        if (!signupForm.reportValidity()) return;

        // Save only public demo profile details; never persist either password field.
        // Read the submitted fields and normalize the email for duplicate checking.
        const formData = new FormData(signupForm);
        const email = String(formData.get("email")).trim().toLowerCase();
        // Load existing local demo accounts before adding another account.
        const users = JSON.parse(localStorage.getItem("courtOneUsers") || "[]");
        // Reject an email already registered in this browser.
        if (users.some((user) => user.email === email)) {
            signupMessage.className = "auth-message alert-warning";
            signupMessage.textContent = "A demo profile with this email already exists in this browser.";
            signupMessage.hidden = false;
            return;
        }

        // Store profile fields only; passwords are deliberately never persisted.
        users.push({
            id: `user-${Date.now()}`,
            name: String(formData.get("name")).trim(),
            email,
            role: String(formData.get("role")),
            businessName: String(formData.get("businessName") || "").trim(),
            active: true,
            createdAt: new Date().toISOString()
        });
        // Persist the updated profile list and reset the form for another entry.
        localStorage.setItem("courtOneUsers", JSON.stringify(users));
        signupForm.reset();
        // Reapply the default role's field visibility after clearing the form.
        updateRoleFields();
        // Tell the user that this browser now has the demo profile.
        signupMessage.className = "auth-message alert-success";
        signupMessage.textContent = "Demo profile created on this device. Sign-in and authentication are not connected in this prototype.";
        signupMessage.hidden = false;
    });
})();