// select.js
document.addEventListener('DOMContentLoaded', function() {
  new SlimSelect({
    select: '#selectElement',
    settings: {
      openPosition: 'down',
      minSelected: 0,
      maxSelected: 100,
      showSearch: true
    },
    events: {
      afterOpen: function() {
        // Asegurar que el dropdown exista antes de estilizarlo
        const dropdown = document.querySelector('.ss-dropdown');
        if (dropdown) { // <-- ¡AÑADE ESTA COMPROBACIÓN!
          dropdown.style.position = 'absolute';
          dropdown.style.zIndex = '9999';
        } else {
          // Opcional: Añadir un log para saber si el elemento no se encontró
          console.warn('SlimSelect dropdown (.ss-dropdown) no encontrado en afterOpen.');
        }
      }
    }
  });
});