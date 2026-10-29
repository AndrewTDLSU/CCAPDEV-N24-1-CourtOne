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

    // Find the registration form before wiring its client-side validation behavior.
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

    // Validate the submitted registration details without sending or storing them.
    signupForm.addEventListener("submit", (event) => {
        // Keep submission on this page; this prototype has no connected account service.
        event.preventDefault();
        // Check the password pair before asking the browser to validate required fields.
        validatePasswords();
        if (!signupForm.reportValidity()) return;

        // Explain that validation passed but account creation is not connected.
        signupMessage.className = "auth-message alert-info";
        signupMessage.textContent = "The form is valid, but account creation is unavailable because no account service is connected.";
        signupMessage.hidden = false;
    });
})();