const usuarioDiv = document.querySelector('.barra-lateral .usuario');

usuarioDiv.addEventListener('mouseenter', () => {
  // Crear el elemento del mensaje
  const tooltip = document.createElement('div');
  tooltip.textContent = 'Click here to log out';
  tooltip.classList.add('tooltip'); // Añadir una clase para el estilo

  // Añadir el mensaje al DOM
  document.body.appendChild(tooltip);

  // Posicionar el mensaje cerca del div .usuario
  const rect = usuarioDiv.getBoundingClientRect();
  tooltip.style.bottom = window.innerHeight - rect.top + 'px'; // Colocar encima del div
  tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 4 + 'px';

  // Agregar estilo para la animación
  setTimeout(() => {
        tooltip.style.opacity = '1';
        tooltip.style.transform = 'translateY(0)';
  }, 10);

  // Eliminar el mensaje
  usuarioDiv.addEventListener('mouseleave', () => {
        tooltip.remove();
  });
});

// Añadir evento de clic para cerrar sesión
usuarioDiv.addEventListener('click', () => {
  // Redirigir a la página de cierre de sesión
  window.location.href = "log_out";
});

const earth = document.getElementById('cloud');

earth.addEventListener('mouseenter', () => {
  // Crear el elemento del mensaje
  const tooltip = document.createElement('div');
  tooltip.textContent = 'Click here minimize side bar';
  tooltip.classList.add('tooltip'); // Añadir una clase para el estilo

  // Añadir el mensaje al DOM
  document.body.appendChild(tooltip);

  // Posicionar el mensaje cerca del div .usuario
  const rect = earth.getBoundingClientRect();
  tooltip.style.bottom = window.innerHeight - rect.bottom / 2 + 'px';
  tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 6 + 'px';

  // Agregar estilo para la animación
  setTimeout(() => {
        tooltip.style.opacity = '1';
        tooltip.style.transform = 'translateY(0)';
  }, 10);

  // Eliminar el mensaje
  earth.addEventListener('mouseleave', () => {
        tooltip.remove();
  });
});

// Añadir CSS para el estilo del tooltip
const style = document.createElement('style');
style.textContent = `
.tooltip {
  position: absolute;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 14px;
  white-space: nowrap;
  z-index: 1000;
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}
`;
document.head.appendChild(style);