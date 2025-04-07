function notify(mensaje) {
  const notification = document.createElement('div');
  notification.textContent = mensaje;
  notification.style.position = 'fixed';
  notification.style.top = '20px'; /* Ajusta la distancia desde la parte superior */
  notification.style.left = '50%';
  notification.style.transform = 'translateX(-50%)';
  notification.style.backgroundColor = '#4CAF50'; /* Color de fondo (verde) */
  notification.style.color = 'white';
  notification.style.padding = '15px';
  notification.style.borderRadius = '5px';
  notification.style.zIndex = '1000'; /* Asegura que esté por encima de otros elementos */
  notification.style.opacity = '1';
  notification.style.transition = 'opacity 0.5s ease-in-out, transform 0.5s ease-in-out';

  document.body.appendChild(notification);

  // Desaparecer la notificación después de 3 segundos
  setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(-50px) translateX(-50%)';
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 500); // Espera a que termine la transición de opacidad
  }, 3000);
}
