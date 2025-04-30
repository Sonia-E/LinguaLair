const events = document.querySelectorAll('.event');
const profile = document.querySelector('.profile');
console.log("saludos desde events.js");

// Open Event's details
events.forEach(event => {
    event.addEventListener("click", () => {
        profile.classList.remove("hidden");
    });
});