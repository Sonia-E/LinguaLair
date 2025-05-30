// Side menu bar
const cloud = document.getElementById("cloud");
const barraLateral = document.querySelector(".barra-lateral");
const spans = document.querySelectorAll("span");
const palanca = document.querySelector(".switch");
const circulo = document.querySelector(".circulo");
const menu = document.querySelector(".menu");
const main = document.querySelector("main");

const lingualair = document.getElementById("lingualair");

lingualair.addEventListener('click', () => {
  window.location.href = "/LinguaLair/";
});

window.addEventListener("load", () => {
    let body = document.body;

    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark-mode");
        if (circulo) {
           circulo.classList.add("pulsado");
        }
    }
});

menu.addEventListener("click",()=>{
    barraLateral.classList.toggle("max-barra-lateral");
    if (barraLateral.classList.contains("max-barra-lateral")) {
        menu.children[0].style.display = "none";
        menu.children[1].style.display = "block";
    } else {
        menu.children[0].style.display = "block";
        menu.children[1].style.display = "none";
    }
    if (window.innerWidth<=320) {
        barraLateral.classList.add("mini-barra-lateral");
        main.classList.add("min-main");
        spans.forEach((span)=>{
            span.classList.add("oculto");
        })
    }
})

// Logcard popup
const botonesLog = document.querySelectorAll(".boton");
const popup = document.getElementById("myPopup");
const overlay = document.getElementById("overlay");
const closeButton = popup.querySelector(".close-button");

botonesLog.forEach(boton => {
    boton.addEventListener("click", () => {
        popup.classList.add("show");
        overlay.style.visibility = "visible";
    });
});

// Cerramos el popup al hacer clic en el overlay
overlay.addEventListener("click", () => {
    popup.classList.remove("show");
    overlay.style.visibility = "hidden";
});

// Cerramos el popup con el botón de dentro
if (closeButton) {
    closeButton.addEventListener("click", () => {
        popup.classList.remove("show");
        overlay.style.visibility = "hidden";
    });
}

palanca.addEventListener("click",()=>{
    let body = document.body;
    body.classList.toggle("dark-mode");
    circulo.classList.toggle("pulsado");

    // Guardamos el estado en localStorage
    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("darkMode", "enabled");
    } else {
        localStorage.setItem("darkMode", "disabled");
    }
})

cloud.addEventListener("click",()=>{
    console.log("Pulsando");
    barraLateral.classList.toggle("mini-barra-lateral");
    main.classList.toggle("min-main");
    spans.forEach((span)=>{
        span.classList.toggle("oculto");
    })
    const pieAreas = document.querySelectorAll('.pie-area');
    const miniBarraLateral = document.querySelector(".mini-barra-lateral");
    pieAreas.forEach(pieArea => {
        if (pieArea && miniBarraLateral) {
            pieArea.style.left = "13%";
        } else if(pieArea && !miniBarraLateral) {
            pieArea.style.left = "18.5%";
        }
    });
})