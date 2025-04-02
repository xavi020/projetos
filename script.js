document.getElementById("search-button").addEventListener("click", function() {
    let searchInput = document.getElementById("search-input").value;
    document.getElementById("search-result").innerText = "Você buscou por: '" + searchInput + "'";
});

let index = 0;
const track = document.querySelector(".carousel-track");
const items = document.querySelectorAll(".carousel-item");
const prev = document.querySelector(".prev");
const next = document.querySelector(".next");

function updateCarousel() {
    track.style.transform = `translateX(${-index * 210}px)`;
}

prev.addEventListener("click", function() {
    if (index > 0) index--;
    updateCarousel();
});

next.addEventListener("click", function() {
    if (index < items.length - 1) index++;
    updateCarousel();
});
