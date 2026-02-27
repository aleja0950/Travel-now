
    const tabs = document.querySelectorAll(".tab");
    const underline = document.querySelector(".underline");
    const contents = document.querySelectorAll(".tab-content");

    function moveUnderline(tab) {
        underline.style.width = tab.offsetWidth + "px";
        underline.style.left = tab.offsetLeft + "px";
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            document.querySelector(".tab.active").classList.remove("active");
            tab.classList.add("active");
            moveUnderline(tab);

            const target = tab.dataset.target;

            contents.forEach(content => {
                content.classList.remove("active");
            });

            document.getElementById(target).classList.add("active");
        });
    });
document.querySelector(".reserve-btn").addEventListener("click", () => {
    alert("Reservation request has been sent!");
});



