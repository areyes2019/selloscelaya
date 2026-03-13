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

<!-- HEADER -->

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

<strong>ORDEN DE COMPRA</strong>

</td>

</tr>
</table>


<!-- DATOS -->

<table width="100%" cellpadding="10">

<tr>

<td width="49%" style="border:1px solid #95a5a6">

<p><strong>Sello Pronto</strong></p>
<p>www.sellopronto.com.mx</p>
<p>Cel: 4613581090</p>
<p>Tel: 461 250 7482</p>
<p>ventas@sellopronto.com.mx</p>

</td>

<td width="2%"></td>

<td width="49%" style="border:1px solid #95a5a6">

<?php foreach ($id_pedido as $key): ?>

<p><strong>Orden No: <?php echo $key['id_pedido'] ?></strong></p>
<p><strong>Fecha: <?php echo $key['created_at'] ?></strong></p>

<?php endforeach ?>

<br>

<?php foreach ($proveedor as $data_proveedor): ?>

<p><?php echo $data_proveedor['empresa'] ?></p>
<p><?php echo $data_proveedor['contacto'] ?></p>
<p><?php echo $data_proveedor['correo'] ?></p>
<p><?php echo $data_proveedor['telefono'] ?></p>

<?php endforeach ?>

</td>

</tr>

</table>


<br><br>


<!-- ITEMS -->

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

<?php endforeach ?>

</tbody>

</table>


<!-- TOTAL -->

<table class="totales">

<tr>
<td><strong>SubTotal</strong></td>
<td align="right">
$<?php echo number_format($sub_total,2) ?>
</td>
</tr>

</table>


<!-- FOOTER -->

<div class="footer-info">

<p><strong>Real del Seminario 122, Valle del Real Celaya, Gto. 38024</strong></p>

<p><strong>RFC: RERA7701272R1 | ventas@sellopronto.com.mx</strong></p>

<p>Cel: 4613581090 | Tel: 461 250 7482</p>

<p style="font-style:italic;margin-top:6px">
La siguiente orden de compra está expresada en pesos mexicanos.
</p>

</div>

</body>
</html>