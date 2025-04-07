document.addEventListener('DOMContentLoaded', function() {
  new SlimSelect({
    select: '#selectElement',
    settings: {
      openPosition: 'down', // 'auto', 'up' o 'down'
      minSelected: 0,
      maxSelected: 100,
      showSearch: true
    },
    events: {
      afterOpen: function() {
        // Asegurar que el dropdown tenga espacio suficiente
        const dropdown = document.querySelector('.ss-dropdown');
        dropdown.style.position = 'absolute';
        dropdown.style.zIndex = '9999';
      }
    }
  });
});