document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".blog-category-tab");
    const contents = document.querySelectorAll(".blog-content");

    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            // Remove active from all tabs
            tabs.forEach((t) => t.classList.remove("active"));

            // Hide all content
            contents.forEach((c) => c.classList.add("hidden"));

            // Activate clicked tab
            this.classList.add("active");

            // Show related content
            const target = this.getAttribute("data-tab");
            document.getElementById(target).classList.remove("hidden");
        });
    });
});
