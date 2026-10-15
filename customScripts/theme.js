document.addEventListener("DOMContentLoaded", function () {
    const darkModeBtn = document.getElementById("darkModeBtn");

    darkModeBtn.addEventListener("click", function () {
        const html = document.documentElement;

        if (html.getAttribute("data-bs-theme") === "dark") {
            html.setAttribute("data-bs-theme", "light");
        } else {
            html.setAttribute("data-bs-theme", "dark");
        }
    });
});