document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("mainContent");
    const footer = document.getElementById("footerContent");
    const toggle = document.getElementById("sidebarToggle");

    let miniSidebar = localStorage.getItem("miniSidebar") === "true";

    function applySidebarState() {
        if (miniSidebar) {
            sidebar.classList.remove("w-72");
            sidebar.classList.add("w-20");

            main.classList.remove("ml-72");
            main.classList.add("ml-20");

            footer.classList.remove("ml-72");
            footer.classList.add("ml-20");
        } else {
            sidebar.classList.remove("w-20");
            sidebar.classList.add("w-72");

            main.classList.remove("ml-20");
            main.classList.add("ml-72");

            footer.classList.remove("ml-20");
            footer.classList.add("ml-72");
        }
    }

    applySidebarState();

    toggle.addEventListener("click", () => {
        miniSidebar = !miniSidebar;

        localStorage.setItem("miniSidebar", miniSidebar);

        applySidebarState();
    });
});
