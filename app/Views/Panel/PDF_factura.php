<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
p    { margin: 0; }

td {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.header-table { width:100%; border-collapse:collapse; margin-bottom:15px; }
.logo         { width:340px; }
.logo-marca   { width:240px; }

.items         { width:100%; border-collapse:collapse; font-size:11px; }
.items td,
.items th      { border:1px solid #95a5a6; padding:6px; }
.items thead   { background:#f5f5f5; font-weight:bold; }

.totales       { width:32%; float:right; border-collapse:collapse; margin-top:10px; }
.totales td    { border:1px solid #95a5a6; padding:7px; }

/* ── Timbre Fiscal Digital ── */
.timbre {
    margin-top:30px;
    border-top:2px solid #2c3e50;
    padding-top:10px;
    font-size:8pt;
}
.timbre-titulo {
    font-weight:bold;
    font-size:8.5pt;
    text-align:center;
    margin-bottom:8px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#2c3e50;
}
.tfd-table {
    width:100%;
    border-collapse:collapse;
}
.tfd-left {
    width:23%;
    vertical-align:top;
    padding-right:8px;
    border-right:1px solid #ccc;
}
.tfd-right {
    width:77%;
    vertical-align:top;
    padding-left:10px;
    max-width:77%;
    overflow:hidden;
}
.tfd-label {
    font-weight:bold;
    font-size:7pt;
    color:#555;
    margin-bottom:1px;
    margin-top:5px;
}
.tfd-label:first-child { margin-top:0; }

/* 🔥 FIX REAL DOMPDF */
.mono-box {
    font-family: DejaVu Sans Mono, monospace;
    font-size:5.8pt;
    line-height:1.25;

    border:1px solid #ddd;
    background:#fafafa;

    padding:4px;
    margin-top:2px;
    margin-bottom:5px;

    width:100%;
    max-width:100%;

    display:block;

    white-space:normal;
    word-wrap:break-word;
    overflow-wrap:break-word;

    page-break-inside: avoid;
}

.mono-sm {
    font-family: DejaVu Sans Mono, monospace;
    font-size:6.5pt;
    word-wrap:break-word;
    white-space:normal;
}

.badge-valid    { background:#27ae60; color:#fff; padding:2px 8px; border-radius:4px; }
.badge-canceled { background:#c0392b; color:#fff; padding:2px 8px; border-radius:4px; }

.footer-info {
    position:fixed; bottom:20px; left:40px; right:40px;
    text-align:center; border-top:1px solid #eee;
    padding-top:8px; font-size:8.5pt;
}
.cfdi-nota {
    text-align:center;
    margin-top:8px;
    font-size:7.5pt;
    color:#666;
    font-style:italic;
    border-top:1px solid #eee;
    padding-top:5px;
}
</style>
</head>
<body>

<?php
/* 🔥 QR + CADENA (CORREGIDO) */
$rfcEmisor = 'RERA7701272R1';
$rfcReceptor = $cliente_rfc ?? '';
$totalFmt = number_format($total, 6, '.', '');
$fe = substr($sello_cfdi, -8);

$urlQR = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=$uuid&re=$rfcEmisor&rr=$rfcReceptor&tt=$totalFmt&fe=$fe";

$qrImage = @file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=" . urlencode($urlQR));
$qr_base64 = $qrImage ? 'data:image/png;base64,'.base64_encode($qrImage) : '';

$cadena_original = "||1.1|$uuid|$fecha_timbrado|$rfc_prov_cert|$sello_cfdi|$sat_cert||";
?>

<!-- ENCABEZADO -->
<table class="header-table">
<tr>
<td width="20%">
<?php
$path = FCPATH.'public/img/logo2.png';
if (file_exists($path)) {
    $ext  = pathinfo($path, PATHINFO_EXTENSION);
    $b64  = 'data:image/'.$ext.';base64,'.base64_encode(file_get_contents($path));
    echo '<img src="'.$b64.'" class="logo">';
}
?>
</td>

<td width="35%">
<?php
$logo = FCPATH.'public/img/pontumarca.png';
if (file_exists($logo)) {
    $ext = pathinfo($logo, PATHINFO_EXTENSION);
    $b64 = 'data:image/'.$ext.';base64,'.base64_encode(file_get_contents($logo));
    echo '<img src="'.$b64.'" class="logo-marca">';
}
?>
</td>

<td width="15%"></td>

<td width="30%" style="text-align:right">
<p style="font-size:18pt;font-weight:bold;color:#2c3e50">FACTURA</p>
<p style="font-size:13pt"><?= esc($serie) ?>-<?= esc($folio) ?></p>
</td>
</tr>
</table>

<!-- DATOS FISCALES: EMISOR / RECEPTOR -->
<?php
$regimenDesc = [
    601 => 'General de Ley Personas Morales',
    603 => 'Personas Morales con Fines no Lucrativos',
    605 => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
    606 => 'Arrendamiento',
    612 => 'Personas Físicas con Actividades Empresariales y Profesionales',
    616 => 'Sin obligaciones fiscales',
    621 => 'Incorporación Fiscal',
    625 => 'Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
    626 => 'Régimen Simplificado de Confianza',
];
$usoDesc = [
    'G01' => 'Adquisición de mercancias',
    'G02' => 'Devoluciones, descuentos o bonificaciones',
    'G03' => 'Gastos en general',
    'I01' => 'Construcciones',
    'I02' => 'Mobiliario y equipo de oficina por inversiones',
    'P01' => 'Por definir',
    'S01' => 'Sin efectos fiscales',
    'CP01'=> 'Pagos',
    'CN01'=> 'Nómina',
];
$estadoLabel = strtolower($estado ?? '') === 'valid' ? 'Vigente' : ucfirst($estado ?? '');
$estadoColor = strtolower($estado ?? '') === 'valid' ? '#27ae60' : '#c0392b';
?>
<table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:9pt">
<tr>
  <!-- EMISOR -->
  <td style="width:48%;vertical-align:top;padding-right:10px">
    <p style="font-weight:bold;color:#2c3e50;border-bottom:1px solid #2c3e50;padding-bottom:3px;margin-bottom:5px">Emisor:</p>
    <p><strong>SELLO PRONTO SA DE CV</strong></p>
    <p>RFC: <strong>RERA7701272R1</strong></p>
    <p>Régimen: 601 - General de Ley Personas Morales</p>
    <p style="margin-top:6px">Fecha: <?= esc($fecha_timbrado) ?></p>
    <p>Estado: <span style="color:<?= $estadoColor ?>;font-weight:bold"><?= esc($estadoLabel) ?></span></p>
  </td>

  <td style="width:4%"></td>

  <!-- RECEPTOR -->
  <td style="width:48%;vertical-align:top;padding-left:10px;border-left:1px solid #ddd">
    <p style="font-weight:bold;color:#2c3e50;border-bottom:1px solid #2c3e50;padding-bottom:3px;margin-bottom:5px">Receptor:</p>
    <p><strong><?= esc($cliente_nombre) ?></strong></p>
    <p><?= esc($cliente_rfc) ?></p>
    <?php if (!empty($cliente_direccion)): ?>
    <p><?= esc($cliente_direccion) ?></p>
    <?php endif; ?>
    <p>Código postal: <?= esc($cliente_cp) ?></p>
    <?php if (!empty($uso_cfdi)): ?>
    <p>Uso del CFDI: <?= esc($uso_cfdi) ?><?= isset($usoDesc[$uso_cfdi]) ? ' - '.esc($usoDesc[$uso_cfdi]) : '' ?></p>
    <?php endif; ?>
    <?php if (!empty($cliente_regimen)): ?>
    <p>Régimen Fiscal: <?= esc((string)$cliente_regimen) ?><?= isset($regimenDesc[(int)$cliente_regimen]) ? ' - '.esc($regimenDesc[(int)$cliente_regimen]) : '' ?></p>
    <?php endif; ?>
    <?php if (!empty($cliente_email)): ?>
    <p>Correo: <?= esc($cliente_email) ?></p>
    <?php endif; ?>
  </td>
</tr>
</table>

<!-- CONCEPTOS -->
<table class="items">
<thead>
<tr>
<th width="8%">Cant.</th>
<th width="8%">Clave SAT</th>
<th width="48%">Descripción</th>
<th width="12%">P/U</th>
<th width="12%">Desc.</th>
<th width="12%">Importe</th>
</tr>
</thead>

<tbody>
<?php foreach ($items as $it):
    $prod    = $it['product'] ?? [];
    $pu      = (float)($prod['price']       ?? $it['unit_price'] ?? 0);
    $qty     = (float)($it['quantity']      ?? 1);
    $desc    = (float)($it['discount']      ?? 0);
    $importe = $pu * $qty - $desc;
?>
<tr>
<td><?= $qty ?></td>
<td><?= esc($prod['product_key'] ?? $it['product_key'] ?? '') ?></td>
<td><?= esc($prod['description'] ?? $it['description'] ?? '') ?></td>
<td>$<?= number_format($pu,2) ?></td>
<td><?= $desc>0 ? '$'.number_format($desc,2) : '—' ?></td>
<td>$<?= number_format($importe,2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- ══ TOTALES ══ -->
<?php
$descuento_total = array_reduce($items, function($carry, $it) {
    return $carry + (float)($it['discount'] ?? 0);
}, 0);
?>
<table style="width:32%;float:right;border-collapse:collapse;margin-top:10px">
<tr>
    <td style="border:1px solid #95a5a6;padding:7px"><strong>Subtotal</strong></td>
    <td style="border:1px solid #95a5a6;padding:7px;text-align:right">$<?= number_format($subtotal, 2) ?></td>
</tr>
<?php if ($descuento_total > 0): ?>
<tr>
    <td style="border:1px solid #95a5a6;padding:7px"><strong>Descuento</strong></td>
    <td style="border:1px solid #95a5a6;padding:7px;text-align:right">-$<?= number_format($descuento_total, 2) ?></td>
</tr>
<?php endif; ?>
<tr>
    <td style="border:1px solid #95a5a6;padding:7px"><strong>IVA (16%)</strong></td>
    <td style="border:1px solid #95a5a6;padding:7px;text-align:right">$<?= number_format($iva, 2) ?></td>
</tr>
<tr style="background:#f5f5f5">
    <td style="border:1px solid #95a5a6;padding:7px"><strong>Total</strong></td>
    <td style="border:1px solid #95a5a6;padding:7px;text-align:right"><strong>$<?= number_format($total, 2) ?></strong></td>
</tr>
</table>
<div style="clear:both;margin-bottom:15px"></div>

<!-- TIMBRE -->
<div class="timbre">

<p class="timbre-titulo">TIMBRE FISCAL DIGITAL</p>

<table class="tfd-table">
<tr>

<td class="tfd-left">

<?php if ($qr_base64): ?>
<img src="<?= $qr_base64 ?>" width="115">
<?php endif; ?>

<p class="tfd-label">UUID</p>
<p class="mono-sm"><?= esc($uuid) ?></p>

<p class="tfd-label">Serie del CSD del SAT</p>
<p class="mono-sm"><?= esc($sat_cert) ?></p>

</td>

<td class="tfd-right">

<p class="tfd-label">Sello CFDI</p>
<div class="mono-box"><?= chunk_split(esc($sello_cfdi),120,"\n") ?></div>

<p class="tfd-label">Sello SAT</p>
<div class="mono-box"><?= chunk_split(esc($sello_sat),120,"\n") ?></div>

<p class="tfd-label">Cadena original</p>
<div class="mono-box"><?= chunk_split(esc($cadena_original),120,"\n") ?></div>

</td>

</tr>
</table>

<p class="cfdi-nota">Este documento es una representación impresa de un CFDI</p>

</div>

</body>
</html>