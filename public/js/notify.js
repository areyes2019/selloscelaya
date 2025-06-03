function notify(mensaje, color = '#4CAF50') {
  const notification = document.createElement('div');
  notification.textContent = mensaje;
  notification.style.position = 'fixed';
  notification.style.bottom = '20px'; // Aparece abajo
  notification.style.right = '20px';  // Aparece a la derecha
  notification.style.backgroundColor = color;
  notification.style.color = 'white';
  notification.style.padding = '15px';
  notification.style.borderRadius = '5px';
  notification.style.zIndex = '9999'; // Más alto para asegurar visibilidad
  notification.style.opacity = '1';
  notification.style.transition = 'opacity 0.5s ease-in-out, transform 0.5s ease-in-out';
  notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
  notification.style.fontFamily = 'Arial, sans-serif';
  notification.style.fontSize = '14px';
  notification.style.minWidth = '200px';
  notification.style.textAlign = 'center';

  document.body.appendChild(notification);

  // Desaparecer la notificación después de 3 segundos
  setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(50px)'; // animación hacia abajo
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 500);
  }, 3000);
}
