
(function () {
    const root = document.documentElement;
    const storageKey = "courtone-theme";
    let savedTheme = "light";

    try {
        savedTheme = localStorage.getItem(storageKey) || "light";
    } catch (error) {
        // The toggle still works if browser storage is unavailable.
    }

    function setTheme(theme) {
        root.setAttribute("data-bs-theme", theme);
        const button = document.getElementById("darkModeBtn");

        if (button) {
            const isDark = theme === "dark";
            button.textContent = isDark ? "Light mode" : "Dark mode";
            button.setAttribute("aria-label", isDark ? "Switch to light mode" : "Switch to dark mode");
            button.setAttribute("aria-pressed", String(isDark));
        }
    }

    setTheme(savedTheme === "dark" ? "dark" : "light");

    document.addEventListener("DOMContentLoaded", function () {
        let button = document.getElementById("darkModeBtn");

        // Add a floating toggle when the page has no button yet.
        if (!button) {
            button = document.createElement("button");
            button.id = "darkModeBtn";
            button.type = "button";
            button.className = "courtone-theme-toggle";
            document.body.appendChild(button);
        }

        setTheme(root.getAttribute("data-bs-theme"));

        button.addEventListener("click", function () {
            const nextTheme = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
            setTheme(nextTheme);

            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (error) {
            }
        });
    });
})();
