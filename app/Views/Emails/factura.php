<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    body        { font-family: Arial, sans-serif; line-height:1.6; color:#333; max-width:600px; margin:0 auto; padding:20px; }
    .header     { background:#2c3e50; color:#fff; padding:20px; text-align:center; border-radius:5px 5px 0 0; }
    .header h2  { margin:0; color:#fff; }
    .content    { padding:25px; border:1px solid #ddd; border-top:none; border-radius:0 0 5px 5px; }
    .logo       { text-align:center; margin-bottom:20px; }
    .info-box   { background:#f8f9fa; border-left:4px solid #2c3e50; padding:15px; margin:15px 0; border-radius:0 4px 4px 0; }
    .info-box p { margin:4px 0; }
    .highlight  { font-size:1.3em; font-weight:bold; color:#2c3e50; }
    .sat-note   { background:#fff8e1; border-left:4px solid #f39c12; padding:12px 15px; margin:15px 0; font-size:0.9em; }
    .footer     { margin-top:20px; font-size:0.85em; color:#888; border-top:1px solid #eee; padding-top:15px; text-align:center; }
    .signature  { margin-top:25px; }
</style>
</head>
<body>

<div class="header">
    <h2>Factura Electrónica <?= esc($serie) ?>-<?= esc($folio) ?></h2>
</div>

<div class="content">

    <?php if (!empty($logo_data)): ?>
    <div class="logo">
        <img src="data:<?= esc($logo_mime) ?>;base64,<?= esc($logo_data) ?>" alt="Sello Pronto" width="160">
    </div>
    <?php endif; ?>

    <p>Estimado(a) <strong><?= esc($cliente_nombre) ?></strong>,</p>

    <p>Adjuntamos su factura electrónica (CFDI 4.0) conforme a los requisitos del SAT. Encontrará dos archivos adjuntos:</p>

    <ul>
        <li><strong>PDF</strong> — Representación impresa del CFDI</li>
        <li><strong>XML</strong> — Comprobante fiscal digital válido ante el SAT</li>
    </ul>

    <div class="info-box">
        <p><strong>Factura:</strong> <?= esc($serie) ?>-<?= esc($folio) ?></p>
        <p><strong>UUID:</strong> <span style="font-family:monospace;font-size:0.9em"><?= esc($uuid) ?></span></p>
        <p><strong>Total:</strong> <span class="highlight">$<?= number_format($total, 2) ?> MXN</span></p>
    </div>

    <div class="sat-note">
        <strong>Nota:</strong> El archivo XML es su comprobante fiscal oficial. Consérvelo para efectos de deducibilidad ante el SAT.
    </div>

    <p>Para cualquier aclaración estamos a sus órdenes:</p>
    <p>
        <strong>Teléfono:</strong> (461) 358 1090<br>
        <strong>Email:</strong> ventas@sellopronto.com.mx<br>
        <strong>Horario:</strong> Lunes a Viernes 9:00 – 18:00 hrs
    </p>

    <div class="signature">
        <p>Atentamente,</p>
        <p><strong>Equipo de Sello Pronto</strong><br>
        <em>"Soluciones que perduran"</em></p>
    </div>

</div>

<div class="footer">
    <p>Este mensaje es confidencial y para uso exclusivo del destinatario.</p>
    <p>© <?= date('Y') ?> Sello Pronto — RFC: RERA7701272R1</p>
</div>

</body>
</html>
