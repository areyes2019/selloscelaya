# Especificación: Integración WhatsApp Business API

**Versión:** 1.0  
**Fecha:** 2026-06-05  
**Estado:** Pendiente de implementación

---

## 1. Resumen

Integrar la Meta Cloud API (WhatsApp Business Platform) directamente en el sistema para permitir el envío de mensajes de WhatsApp desde las vistas de **Cotizaciones**, **Facturas** y **Órdenes de Compra**, sin uso de intermediarios (Twilio, etc.).

---

## 2. Alcance

### 2.1 Vistas con botón de WhatsApp

| Vista | Contexto del mensaje |
|---|---|
| Cotizaciones | Notificar al cliente sobre su cotización |
| Facturas | Notificar al cliente sobre su factura |
| Órdenes de Compra | Notificar al proveedor/cliente sobre la orden |

### 2.2 Fuera de alcance (por ahora)
- Clientes, Artículos, Gastos, Inventario, POS
- Recepción de mensajes / webhook
- Historial de mensajes enviados

---

## 3. API

- **Proveedor:** Meta Cloud API (WhatsApp Business Platform)
- **Endpoint base:** `https://graph.facebook.com/v19.0/{PHONE_NUMBER_ID}/messages`
- **Método:** POST con JSON
- **Autenticación:** Bearer token (Access Token permanente de Meta)
- **Sin intermediarios:** Llamada directa via cURL/Guzzle desde PHP

---

## 4. Configuración (.env)

```
WHATSAPP_TOKEN=your_permanent_access_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_API_VERSION=v19.0
```

---

## 5. Comportamiento del botón

### 5.1 Visibilidad
- El botón de WhatsApp **solo se muestra** si el registro tiene un número de teléfono registrado.
- Si no hay teléfono, el botón no se renderiza (no se deshabilita, directamente no aparece).

### 5.2 Apariencia
- Ícono de WhatsApp (SVG o FontAwesome) en color verde `#25D366`.
- Se ubica junto a los botones de acción existentes (ver, editar, PDF, etc.) en la fila de cada registro en la tabla.

### 5.3 Acción al hacer clic
1. Se muestra un modal de confirmación con:
   - Número de teléfono destino (ya formateado)
   - Vista previa del mensaje que se enviará
   - Botón **Enviar** y botón **Cancelar**
2. Al confirmar, se hace una llamada AJAX al backend.
3. El backend llama a la Meta Cloud API.
4. Se muestra un **toast de éxito o error** según la respuesta.

---

## 6. Formato del número de teléfono

- El sistema toma el teléfono del campo existente en la BD.
- Se limpia el número: se eliminan espacios, guiones, paréntesis y el prefijo `+`.
- Si el número tiene 10 dígitos (sin código de país), se le antepone `52` (México).
- Formato final esperado por Meta: `521XXXXXXXXXX` o `52XXXXXXXXXX`.
- Si el número ya comienza con `52`, no se duplica el prefijo.

---

## 7. Plantillas de mensajes (Templates)

> Meta requiere plantillas aprobadas para mensajes iniciados por la empresa (Business-Initiated).  
> Las plantillas deben crearse y aprobarse en el Meta Business Manager antes de usarlas.

### 7.1 Template: Cotización

**Nombre sugerido:** `notificacion_cotizacion`  
**Variables:** `{{1}}` = número de cotización, `{{2}}` = monto total

```
Hola, te informamos que tu cotización #{{1}} por un total de ${{2}} MXN 
está lista. Para más información contáctanos.
```

### 7.2 Template: Factura

**Nombre sugerido:** `notificacion_factura`  
**Variables:** `{{1}}` = folio de factura, `{{2}}` = monto total

```
Hola, tu factura con folio {{1}} por ${{2}} MXN ha sido generada. 
Puedes solicitar el PDF a este número. Gracias.
```

### 7.3 Template: Orden de Compra

**Nombre sugerido:** `notificacion_orden_compra`  
**Variables:** `{{1}}` = número de orden, `{{2}}` = monto total

```
Hola, te compartimos que la orden de compra #{{1}} por ${{2}} MXN 
ha sido registrada. Para dudas contáctanos.
```

---

## 8. Flujo técnico

```
[Usuario hace clic en botón WhatsApp]
        ↓
[Modal de confirmación con preview del mensaje]
        ↓
[Usuario confirma → AJAX POST a /admin/whatsapp/send]
        ↓
[WhatsappController recibe: tipo, id_registro]
        ↓
[Consulta BD → obtiene teléfono y datos del registro]
        ↓
[Formatea número al estándar internacional]
        ↓
[Construye payload con template + variables]
        ↓
[HTTP POST a Meta Cloud API con Bearer Token]
        ↓
[Meta responde 200 OK o error]
        ↓
[Controller retorna JSON → Frontend muestra toast]
```

---

## 9. Endpoint del backend

**Ruta:** `POST /admin/whatsapp/send`  
**Controlador:** `App\Controllers\Admin\WhatsappController::send()`

**Request body (JSON):**
```json
{
  "tipo": "cotizacion|factura|orden_compra",
  "id": 123
}
```

**Response exitosa:**
```json
{
  "success": true,
  "message": "Mensaje enviado correctamente"
}
```

**Response con error:**
```json
{
  "success": false,
  "message": "No se pudo enviar el mensaje: [detalle del error de Meta]"
}
```

---

## 10. Archivos a crear / modificar

### Nuevos
| Archivo | Descripción |
|---|---|
| `app/Controllers/Admin/WhatsappController.php` | Controlador que recibe el AJAX y llama a la API |
| `app/Services/WhatsappService.php` | Servicio que encapsula la llamada HTTP a Meta |

### Modificados
| Archivo | Cambio |
|---|---|
| `app/Config/Routes.php` | Agregar ruta `POST admin/whatsapp/send` |
| `app/Views/Panel/cotizaciones.php` | Agregar botón WhatsApp por fila |
| `app/Views/Panel/facturas.php` | Agregar botón WhatsApp por fila |
| `app/Views/Panel/compras.php` | Agregar botón WhatsApp por fila |
| `public/js/admin.js` (o archivo nuevo) | Lógica AJAX + modal de confirmación + toast |
| `.env` | Agregar variables de WhatsApp |

---

## 11. Requisitos previos (fuera del código)

- [ ] Cuenta de Meta Business Manager activa
- [ ] WhatsApp Business Account vinculada
- [ ] Número de teléfono registrado y verificado en Meta
- [ ] Access Token permanente generado (o de larga duración)
- [ ] Phone Number ID obtenido desde el panel de Meta
- [ ] Las 3 plantillas de mensaje creadas y **aprobadas** por Meta antes del primer envío

---

## 12. Criterios de aceptación

- [ ] El botón WhatsApp aparece en las tablas de Cotizaciones, Facturas y Órdenes de Compra
- [ ] El botón NO aparece si el registro no tiene teléfono asociado
- [ ] Al hacer clic aparece un modal con el número y el preview del mensaje
- [ ] Al confirmar se envía el mensaje vía Meta Cloud API
- [ ] Se muestra toast verde en éxito y toast rojo en error
- [ ] El número se formatea correctamente al estándar internacional mexicano
- [ ] Las credenciales NO están hardcodeadas, viven en `.env`
