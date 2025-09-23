<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10pt;
        position: relative;
    }

    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 80px;
        color: rgba(0, 128, 0, 0.1);
        font-weight: bold;
        z-index: -1;
        pointer-events: none;
    }
    .footer-info {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 20px;
        text-align: center;
        padding: 15px 0;
        border-top: 1px solid #eee;
        margin: 0 90px;
    }
    p {
        margin: 0pt;
    }

    table.items {
        border: 0.1mm solid #e7e7e7;
    }

    td {
        vertical-align: top;
    }

    .items td {
        border-left: 0.1mm solid #e7e7e7;
        border-right: 0.1mm solid #e7e7e7;
    }

    table thead td {
        text-align: center;
        border: 0.1mm solid #e7e7e7;
    }

    .items td.blanktotal {
        background-color: #FFFFFF;
        border: 0mm none #e7e7e7;
        border-top: 0.1mm solid #e7e7e7;
        border-right: 0.1mm solid #e7e7e7;
    }

    .items td.totals {
        text-align: right;
        border: 0.1mm solid #e7e7e7;
    }

    .items td.cost {
        text-align: center;
    }
    
    .recibo-section {
        margin-top: 50px;
        page-break-before: always;
    }
    
    .recibo-title {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .recibo-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .recibo-table td {
        padding: 10px;
        border: 1px solid #95a5a6;
    }
    
    .recibo-table .label {
        font-weight: bold;
        width: 40%;
    }
    </style>
</head>

<body>
    <?php if ($pagado): ?>
    <div class="watermark">PAGADO</div>
    <?php endif; ?>

    <table width="100%" cellpadding="10">
        <tr>
            <td width="10%" style="padding: 0px; text-align: left;">
                <?php
                    $path = FCPATH . 'public/img/logo2.png';
                    if(file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        echo '<img src="'.$base64.'" alt="logo" width="150" height="150">';
                    }
                ?>
            </td>
            <td width="10%" style="padding: 0px; text-align: left;">
                <?php
                    $logo = FCPATH . 'public/img/pontumarca.png';
                    if(file_exists($logo)) {
                        $ty = pathinfo($logo, PATHINFO_EXTENSION);
                        $file = file_get_contents($logo);
                        $ba = 'data:image/' . $ty . ';base64,' . base64_encode($file);
                        echo '<img src="'.$ba.'" alt="marca" width="260" style="margin-top: 50px;">';
                    }
                ?>
            </td>
            <td width="40%">&nbsp;</td>
            <td width="40%" style="text-align: left;">
                <p style="font-weight: bolder;">DATOS DE PAGO</p>
                <?php
                    $pat = FCPATH . 'public/img/pagos.png';
                    if(file_exists($pat)) {
                        $typ = pathinfo($pat, PATHINFO_EXTENSION);
                        $dat = file_get_contents($pat);
                        $base = 'data:image/' . $typ . ';base64,' . base64_encode($dat);
                        echo '<img src="'.$base.'" alt="pagos" width="250" style="margin-top: 5px;">';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td height="10" style="font-size: 0px; line-height: 10px; padding: 0px;">&nbsp;</td>
        </tr>
    </table>

    <table width="100%" cellpadding="10">
        <tr>
            <td width="49%" style="border: 0.5mm solid #95a5a6;">
                <p><strong>Sello Pronto</strong></p>
                <p>www.sellopronto.com.mx</p>
                <p>Cel: 4613581090</p>
                <p>Tel: 461 250 7482</p>
                <p>ventas@gmail.com</p>
            </td>
            <td width="2%">&nbsp;</td>
            <td width="49%" style="border: 0.5mm solid #95a5a6; text-align: left;">
                <p><strong>Cotización No: <?php echo $id_cotizacion ?> </strong></p>
                <p><strong>Fecha: <?php echo $cot['created_at'] ?> </strong></p>
                <p><strong>Válida hasta: <?php echo $cot['caduca'] ?> </strong></p>

                <p style="margin-top: 10px;"><?php echo $cliente['nombre'] ?></p>
                <p><?php echo $cliente['correo'] ?></p>
                <p><?php echo $cliente['telefono'] ?></p>
            </td>
        </tr>
    </table>

    <!-- El resto de tu HTML permanece igual -->
    <!-- ... -->
</body>
</html>