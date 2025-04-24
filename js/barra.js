// Side menu bar
const cloud = document.getElementById("cloud");
const barraLateral = document.querySelector(".barra-lateral");
const spans = document.querySelectorAll("span");
const palanca = document.querySelector(".switch");
const circulo = document.querySelector(".circulo");
const menu = document.querySelector(".menu");
const main = document.querySelector("main");

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



// Cerrar el popup al hacer clic en el overlay
    overlay.addEventListener("click", () => {
    popup.classList.remove("show");
    overlay.style.visibility = "hidden";
});

// Opcional: Cerrar el popup con un botón dentro
if (closeButton) {
    closeButton.addEventListener("click", () => {
        popup.classList.remove("show");
        overlay.style.visibility = "hidden";
    });
}

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

palanca.addEventListener("click",()=>{
    let body = document.body;
    body.classList.toggle("dark-mode");
    circulo.classList.toggle("pulsado");
})

cloud.addEventListener("click",()=>{
    barraLateral.classList.toggle("mini-barra-lateral");
    main.classList.toggle("min-main");
    spans.forEach((span)=>{
        span.classList.toggle("oculto");
    })
})


// function getFlagEmoji(countryCode) {
//     return [...countryCode.toUpperCase()].map(char => 
//         String.fromCodePoint(127397 + char.charCodeAt())
//     ).reduce((a, b) => `${a}${b}`);
//   }
  
//   const flagReplace = document.querySelectorAll('[data-flag]');
//   flagReplace.forEach(s => s.innerHTML = getFlagEmoji(s.dataset.flag))
{/* <span data-flag="GB"></span>
<span data-flag="US"></span>
<span data-flag="CA"></span>
<span data-flag="FR"></span> */}