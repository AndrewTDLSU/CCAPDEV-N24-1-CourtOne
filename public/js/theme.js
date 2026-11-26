
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
            button.setAttribute("aria-label", isDark ? "Switch to light mode" : "Switch to dark mode");
            button.setAttribute("aria-pressed", String(isDark));
        }
    }

    setTheme(savedTheme === "dark" ? "dark" : "light");

    document.addEventListener("DOMContentLoaded", function () {
        const button = document.getElementById("darkModeBtn");
        if (!button) return;

        setTheme(root.getAttribute("data-bs-theme"));

        button.addEventListener("click", function () {
            const nextTheme = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
            setTheme(nextTheme);
            document.dispatchEvent(new Event('court:theme-change'));

            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (error) {
            }
        });
    });
})();
