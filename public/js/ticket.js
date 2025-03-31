$(document).ready(function() {
    let itemIndex = initialItemIndex;

    // Inicialización
    updateTotal();
    updateTicketPreview();

    // Añadir Item
    $('#btn-add-item').on('click', function() {
        const descripcion = $('#item_descripcion').val().trim();
        const cantidad = parseInt($('#item_cantidad').val());
        const precio = parseFloat($('#item_precio').val());

        if (!descripcion || isNaN(cantidad) || cantidad <= 0 || isNaN(precio) || precio < 0) {
            alert('Por favor, complete la descripción, cantidad válida y precio válido.');
            return;
        }

        const subtotal = (cantidad * precio).toFixed(2);
        const precioFormatted = precio.toFixed(2);

        const newRow = `
            <tr class="item-row">
                <td>
                    ${descripcion}
                    <input type="hidden" name="detalle[${itemIndex}][descripcion]" value="${descripcion}">
                </td>
                <td class="cantidad">
                    ${cantidad}
                    <input type="hidden" name="detalle[${itemIndex}][cantidad]" value="${cantidad}">
                </td>
                <td class="precio-unitario">
                    ${precioFormatted}
                    <input type="hidden" name="detalle[${itemIndex}][precio_unitario]" value="${precio}">
                </td>
                <td class="subtotal">${subtotal}</td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove-item">Quitar</button></td>
            </tr>
        `;

        $('#items-list').append(newRow);
        itemIndex++;

        // Limpiar campos
        $('#item_descripcion').val('');
        $('#item_cantidad').val('1');
        $('#item_precio').val('');
        $('#item_descripcion').focus();

        updateTotal();
        updateTicketPreview();
    });

    // Quitar Item
    $('#items-table').on('click', '.btn-remove-item', function() {
        $(this).closest('tr').remove();
        updateTotal();
        updateTicketPreview();
    });

    // Actualizar Total y Saldo
    function updateTotal() {
        let total = 0;
        $('#items-list tr').each(function() {
            const subtotalText = $(this).find('.subtotal').text().replace(',', '');
            const subtotal = parseFloat(subtotalText);
            if (!isNaN(subtotal)) {
                total += subtotal;
            }
        });
        
        const anticipo = parseFloat($('#anticipo').val()) || 0;
        const saldo = Math.max(0, total - anticipo);
        
        $('#total-display').html(`<strong>${total.toFixed(2)}</strong>`);
        $('#total_final_hidden').val(total.toFixed(2));
        $('#resumen-total').text(total.toFixed(2));
        $('#resumen-saldo').text(saldo.toFixed(2));
    }

    // Actualizar Ticket Preview
    function updateTicketPreview() {
        // Actualizar solo información del cliente
        $('#ticket-cliente-nombre').text($('#cliente_nombre').val() || '[Cliente no ingresado]');
        $('#ticket-cliente-tel').text($('#cliente_telefono').val() || '');
        
        // Actualizar fecha
        $('#ticket-fecha').text(new Date().toLocaleString('es-VE'));
    }

    // Actualizar Items del Ticket (función separada)
    function updateTicketItems() {
        const itemsHtml = [];
        $('#items-list tr').each(function() {
            const desc = $(this).find('td:nth-child(1)').text().trim();
            const cant = $(this).find('.cantidad').text().trim();
            const pu = $(this).find('.precio-unitario').text().trim();
            const subt = $(this).find('.subtotal').text().trim();

            const itemLine = `
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 2px; word-break: break-word;">
                    <span style="width: 15%; text-align: right;">${cant}</span>
                    <span style="width: 45%; padding-left: 5px;">${desc}</span>
                    <span style="width: 20%; text-align: right;">${pu}</span>
                    <span style="width: 20%; text-align: right;">${subt}</span>
                </div>`;
            itemsHtml.push(itemLine);
        });
        
        $('#ticket-items-list').html(itemsHtml.join(''));
        
        // Actualizar totales en el ticket
        const total = parseFloat($('#total-display').text().replace(/[^0-9.]/g, '')) || 0;
        const anticipo = parseFloat($('#anticipo').val()) || 0;
        const saldo = Math.max(0, total - anticipo);
        
        const pagosHtml = `
            <hr style="border-top: 1px dashed #ccc;">
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Total:</strong></span>
                <span>${total.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Anticipo:</strong></span>
                <span>${anticipo.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Saldo:</strong></span>
                <span>${saldo.toFixed(2)}</span>
            </div>
        `;
        
        $('#ticket-preview').find('.pagos-section').remove();
        $('#ticket-items-list').after(`<div class="pagos-section">${pagosHtml}</div>`);
    }

    // Event listeners modificados
    $('#cliente_nombre, #cliente_telefono').on('input', function() {
        // Solo actualiza la información del cliente, no los items
        $('#ticket-cliente-nombre').text($('#cliente_nombre').val() || '[Cliente no ingresado]');
        $('#ticket-cliente-tel').text($('#cliente_telefono').val() || '');
    });

    $('#anticipo').on('input', function() {
        updateTotal();
        // Actualizar solo la sección de pagos del ticket
        const total = parseFloat($('#total-display').text().replace(/[^0-9.]/g, '')) || 0;
        const anticipo = parseFloat($(this).val()) || 0;
        const saldo = Math.max(0, total - anticipo);
        
        const pagosHtml = `
            <hr style="border-top: 1px dashed #ccc;">
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Total:</strong></span>
                <span>${total.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Anticipo:</strong></span>
                <span>${anticipo.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span><strong>Saldo:</strong></span>
                <span>${saldo.toFixed(2)}</span>
            </div>
        `;
        
        $('#ticket-preview').find('.pagos-section').remove();
        $('#ticket-items-list').after(`<div class="pagos-section">${pagosHtml}</div>`);
    });

    // Inicialización correcta
    updateTicketItems();
});