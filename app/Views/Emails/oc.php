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
        <h2 style="margin:0; color:white;">Orden de Compra #<?= esc($id) ?></h2>
    </div>
    
    <div class="content">
        <div class="logo">
            <img src="data:<?= esc($logo_mime) ?>;base64,<?= esc($logo_data) ?>" alt="Logo Sello Pronto" width="150">
        </div>
        
        <p>Estimado Proveedor,</p>
        
        <p>Adjunto encontrará su orden <strong>OC-<?= esc($id) ?></strong> con los detalles de nuestro pedido.</p>
        
        
        <div class="validity">
            <h4 style="margin-top:0;">Validez de la cotización:</h4>
            <p>Esta orden tiene una validez de <strong>30 días naturales</strong> a partir de la fecha de emisión.</p>
        </div>
        
        <p>Para cualquier aclaración, estamos a sus órdenes en:</p>
        <p>
            <strong>Teléfono:</strong> (461) 358 1090<br>
            <strong>Email:</strong> ventas@sellopronto.com.mx<br>
            <strong>Horario:</strong> Lunes a Viernes 9:00 - 18:00 hrs
        </p>
        
        <div class="signature">
            <p>Atentamente,</p>
            <p><strong>Equipo de Sello Pronto</strong><br>
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