<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>

body{
font-family: DejaVu Sans, sans-serif;
font-size:10pt;
}

p{
margin:0;
}

.header-table{
width:100%;
border-collapse:collapse;
margin-bottom:15px;
}

.logo{
width:340px;
}

.logo-marca{
width:240px;
}

.logo-pagos{
width:220px;
margin-top:5px;
}

.watermark{
position:fixed;
top:40%;
left:20%;
font-size:90px;
color:rgba(0,128,0,0.08);
transform:rotate(-40deg);
}

.items{
width:100%;
border-collapse:collapse;
font-size:13px;
}

.items td,
.items th{
border:1px solid #95a5a6;
padding:8px;
}

.items thead{
background:#f5f5f5;
font-weight:bold;
}

.totales{
width:30%;
float:right;
border-collapse:collapse;
margin-top:10px;
}

.totales td{
border:1px solid #95a5a6;
padding:8px;
}

.recibo-section{
page-break-before:always;
margin-top:40px;
}

.recibo-table{
width:100%;
border-collapse:collapse;
}

.recibo-table td{
border:1px solid #95a5a6;
padding:10px;
}

.footer-info{
position:fixed;
bottom:20px;
left:40px;
right:40px;
text-align:center;
border-top:1px solid #eee;
padding-top:10px;
font-size:9pt;
}

</style>
</head>

<body>

<?php if ($pagado): ?>
<div class="watermark">PAGADO</div>
<?php endif; ?>

<table class="header-table">
<tr>

<td width="20%">
<?php
$path = FCPATH.'public/img/logo2.png';
if(file_exists($path)){
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
echo '<img src="'.$base64.'" class="logo">';
}
?>
</td>

<td width="35%">
<?php
$logo = FCPATH.'public/img/pontumarca.png';
if(file_exists($logo)){
$type = pathinfo($logo, PATHINFO_EXTENSION);
$data = file_get_contents($logo);
$base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
echo '<img src="'.$base64.'" class="logo-marca">';
}
?>
</td>

<td width="15%"></td>

<td width="30%">
<strong>DATOS DE PAGO</strong>

<?php
$pagos = FCPATH.'public/img/pagos.png';
if(file_exists($pagos)){
$type = pathinfo($pagos, PATHINFO_EXTENSION);
$data = file_get_contents($pagos);
$base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
echo '<br><img src="'.$base64.'" class="logo-pagos">';
}
?>
</td>

</tr>
</table>

<table width="100%" cellpadding="10">

<tr>

<td width="49%" style="border:1px solid #95a5a6">
<p><strong>Sello Pronto</strong></p>
<p>www.sellopronto.com.mx</p>
<p>Cel: 4613581090</p>
<p>Tel: 461 250 7482</p>
<p>ventas@gmail.com</p>
</td>

<td width="2%"></td>

<td width="49%" style="border:1px solid #95a5a6">

<p><strong>Cotización No: <?php echo $id_cotizacion ?></strong></p>
<p><strong>Fecha: <?php echo $cot['created_at'] ?></strong></p>
<p><strong>Válida hasta: <?php echo $cot['caduca'] ?></strong></p>

<br>

<p><?php echo $cliente['nombre'] ?></p>
<p><?php echo $cliente['correo'] ?></p>
<p><?php echo $cliente['telefono'] ?></p>

</td>

</tr>

</table>

<br><br>

<table class="items">

<thead>

<tr>
<th width="50%" align="left">Artículo</th>
<th width="20%">Modelo</th>
<th width="10%">Cant.</th>
<th width="10%">P/U</th>
<th width="10%">Total</th>
</tr>

</thead>

<tbody>

<?php foreach ($detalles as $linea): ?>

<tr>

<td><?php echo $linea['nombre'] ?></td>

<td><?php echo $linea['modelo'] ?></td>

<td align="center">
<?php echo $linea['cantidad'] ?>
</td>

<td>
$<?php echo number_format($linea['p_unitario'],2) ?>
</td>

<td>
$<?php echo number_format($linea['total'],2) ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<table class="totales">

<tr>
<td><strong>Sub-Total</strong></td>
<td align="right">$<?php echo $sub_total ?></td>
</tr>

<tr>
<td><strong>Dcto</strong></td>
<td align="right">$<?php echo $descuento ?></td>
</tr>

<tr>
<td><strong>IVA</strong></td>
<td align="right">$<?php echo $iva ?></td>
</tr>

<tr>
<td><strong>Total</strong></td>
<td align="right"><strong>$<?php echo $total ?></strong></td>
</tr>

<?php if($anticipo>0): ?>

<tr>
<td><strong>Anticipo</strong></td>
<td align="right">
$<?php echo number_format($anticipo,2) ?>
</td>
</tr>

<tr>
<td><strong>Saldo</strong></td>
<td align="right">
$<?php echo number_format(($total-$anticipo),2) ?>
</td>
</tr>

<?php endif; ?>

</table>

<?php if (isset($anticipo) && $anticipo > 0 && !$pagado): ?>

<div class="recibo-section">

<h2 style="text-align:center">RECIBO DE ANTICIPO</h2>

<table width="100%" cellpadding="10">

<tr>

<td width="49%" style="border:1px solid #95a5a6">

<p><strong>Sello Pronto</strong></p>
<p>www.sellopronto.com.mx</p>
<p>Cel: 4613581090</p>
<p>Tel: 461 250 7482</p>
<p>ventas@gmail.com</p>

</td>

<td width="2%"></td>

<td width="49%" style="border:1px solid #95a5a6">

<p><strong>Recibo No: <?php echo $id_cotizacion.'-A' ?></strong></p>
<p><strong>Fecha: <?php echo date('Y-m-d H:i:s') ?></strong></p>
<p><strong>Cotización No: <?php echo $id_cotizacion ?></strong></p>

</td>

</tr>

</table>

<table class="recibo-table">

<tr>
<td width="40%"><strong>Recibí de:</strong></td>
<td><?php echo $cliente['nombre'] ?></td>
</tr>

<tr>
<td><strong>La cantidad de:</strong></td>
<td>$<?php echo number_format((float)$anticipo,2) ?></td>
</tr>

<tr>
<td><strong>Por concepto de:</strong></td>
<td>Anticipo para cotización No. <?php echo $id_cotizacion ?></td>
</tr>

<tr>
<td><strong>Saldo pendiente:</strong></td>
<td>$<?php echo number_format(($total-$anticipo),2) ?></td>
</tr>

<tr>
<td><strong>Forma de pago:</strong></td>
<td><?php echo isset($forma_pago) ? $forma_pago : 'No especificado' ?></td>
</tr>

</table>

<br><br>

<table width="100%">

<tr>

<td width="50%" align="center">

<br><br>

_________________________

<p>Recibí conforme</p>
<p><?php echo $cliente['nombre'] ?></p>

</td>

<td width="50%" align="center">

<br><br>

_________________________

<p>Entregó</p>
<p>Sello Pronto</p>

</td>

</tr>

</table>

</div>

<?php endif; ?>

<div class="footer-info">

<p><strong>Real del Seminario 122, Valle del Real Celaya, Gto. 38024</strong></p>

<p><strong>RFC: RERA7701272R1 | ventas@sellopronto.com.mx</strong></p>

<p>Cel: 4613581090 | Tel: 461 250 7482</p>

<p style="font-style:italic;margin-top:6px">
La siguiente cotización está expresada en pesos mexicanos.
Una vez confirmado el pago, comenzamos a trabajar en tu diseño.
Nunca fabricamos nada sin tu aprobación.
</p>

</div>

</body>
</html>