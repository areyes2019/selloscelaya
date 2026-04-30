<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0066cc;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .bank-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #0066cc;
            margin: 15px 0;
        }
        .validity {
            background-color: #fff8e1;
            padding: 10px;
            border-left: 4px solid #ffc107;
            font-size: 0.9em;
        }
        .logo {
            text-align: center;
            margin: 15px 0;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; color:white;">Cotización #<?= esc($id) ?></h2>
    </div>
    
    <div class="content">
        <div class="logo">
            <img src="cid:<?= esc($cid_logo) ?>" alt="Logo Sello Pronto" width="150">
        </div>
        
        <p>Estimado cliente,</p>
        
        <p>Adjunto encontrará su cotización <strong>QT-<?= esc($id) ?></strong> con los detalles de su pedido.</p>
        
        <div class="bank-info">
            <h3 style="margin-top:0;">Datos Bancarios:</h3>
            <p><strong>Banco:</strong> BBVA</p>
            <p><strong>Beneficiario:</strong>Abdias Reyes Reyna</p>
            <p><strong>CLABE:</strong> 012 1800 1423 6669 805</p>
            <p><strong>Cuenta:</strong> 1423666980</p>
            <p><strong>Referencia:</strong> QT-<?= esc($id) ?></p>
        </div>
        
        <div class="validity">
            <h4 style="margin-top:0;">Validez de la cotización:</h4>
            <p>Esta cotización tiene una validez de <strong>30 días naturales</strong> a partir de la fecha de emisión. Los precios están sujetos a cambios sin previo aviso por fluctuaciones en el costo de materiales o condiciones del mercado.</p>
        </div>
        
        <h3>Condiciones de compra:</h3>
        <ul>
            <li>Precios en moneda nacional (MXN)</li>
            <li>Incluye IVA (16%)</li>
            <li>Tiempo de entrega: 3-5 días hábiles después de confirmación de pago</li>
            <li>Métodos de pago: Transferencia electrónica, depósito bancario</li>
            <li>Para pedidos especiales se requiere 50% de anticipo</li>
        </ul>
        
        <p>Para cualquier aclaración, estamos a sus órdenes en:</p>
        <p>
            <strong>Teléfono:</strong> (461) 358 1090<br>
            <strong>Email:</strong> ventas@sellopronto.com.mx<br>
            <strong>Horario:</strong> Lunes a Viernes 9:00 - 18:00 hrs
        </p>
        
        <div class="signature">
            <p>Atentamente,</p>
            <p><strong>Equipo de Ventas</strong><br>
            Sello Pronto<br>
            <em>"Soluciones que perduran"</em></p>
        </div>
    </div>
    
    <div class="footer">
        <p>Este mensaje es confidencial y para uso exclusivo del destinatario. Si lo recibió por error, por favor notifíquelo y elimínelo.</p>
        <p style="text-align:center;">© <?= date('Y') ?> Sello Pronto. Todos los derechos reservados.</p>
    </div>
</body>
</html>