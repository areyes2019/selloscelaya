<html>
<head>
    <style>
    body {
        font-family: sans-serif;
        font-size: 10pt;
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
        background-color: #EEEEEE;
        border: 0.1mm solid #e7e7e7;
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
        text-align: "."center;
    }
    </style>
</head>

<body>
    <table width="100%" style="font-family: sans-serif;" cellpadding="10">
        <tr>
            <td width="10%" style="padding: 0px; text-align: left;">
                <?php
                    $path = base_url('public/img/logo2.png');
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                ?>
                <img src="<?php echo $base64; ?>" alt="logo" align="center" width="150" height="150">
            </td>
            <td width="10%" style="padding: 0px; text-align: left;">
                <?php
                    $logo = base_url('public/img/pontumarca.png');
                    $ty = pathinfo($logo, PATHINFO_EXTENSION);
                    $file = file_get_contents($logo);
                    $ba = 'data:image/' . $ty . ';base64,' . base64_encode($file);
                ?>
                <img src="<?php echo $ba; ?>" alt="logo" align="center" width="260" style="margin-top: 50px;">
            </td>
            <td width="40%">&nbsp;</td>
            <td width="40%" style="text-align: left;">
            </td>
        </tr>
        <tr>
          <td height="10" style="font-size: 0px; line-height: 10px; height: 10px; padding: 0px;">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" style="font-family: sans-serif;" cellpadding="10">
        <tr>
            <td width="49%" style="border: 0.5mm solid #95a5a6;">
                <p><strong>Sello Pronto</strong></p>
                <p>www.sellopronto.com.mx</p>
                <p>Cel: 4613581090</p>
                <p>Tel:461 250 7482</p>
                <p>ventas@gmail.com</p>
            </td>
            <td width="2%">&nbsp;</td>
            <td width="49%" style="border: 0.5mm solid #95a5a6; text-align: left;">
                <?php foreach ($id_pedido as $key): ?>
                <p><strong>Orden No: <?php echo $key['pedidos_id'] ?> </strong></p>   
                <p><strong>Fecha: <?php echo $key['fecha'] ?> </strong></p>   
                <?php endforeach ?>
                <?php foreach ($proveedor as $data_proveedor): ?>
                <p style="margin-top: 10px;"><?php echo $data_proveedor['empresa'] ?></p>
                <p style="margin-top: 10px;"><?php echo $data_proveedor['contacto'] ?></p>
                <p><?php echo $data_proveedor['correo'] ?></p>
                <p><?php echo $data_proveedor['telefono'] ?></p>
                <?php endforeach ?>
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table class="items" width="100%" style="font-size: 14px; border-collapse: collapse;"  cellpadding="8">
        <thead>
            <tr>
                <td width="50%" style="text-align: left; border: 1px solid #95a5a6;"><strong>Artículo</strong></td>
                <td width="20%" style="text-align: left;border: 1px solid #95a5a6"><strong>Modelo</strong></td>
                <td width="6%" style="text-align: center;border: 1px solid #95a5a6"><strong>Cant.</strong></td>
                <td width="12%" style="text-align: left;border: 1px solid #95a5a6"><strong>P/U</strong></td>
                <td width="12%" style="text-align: left;border: 1px solid #95a5a6"><strong>TOTAL</strong></td>
            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            <?php foreach ($detalles as $linea): ?>
            <tr>
                <td style="padding: 9px 7px; line-height: 20px; border: 1px solid #95a5a6; "><?php echo $linea['nombre'] ?></td>
                <td style="padding: 9px 7px; line-height: 20px; border: 1px solid #95a5a6; "><?php echo $linea['modelo'] ?></td>
                <td style="padding: 9px 7px; line-height: 20px; border: 1px solid #95a5a6; text-align: center;"><?php echo $linea['cantidad'] ?></td>
                <td style="padding: 9px 7px; line-height: 20px; border: 1px solid #95a5a6; ">$<?php echo number_format($linea['p_unitario'],2)  ?></td>
                <td style="padding: 9px 7px; line-height: 20px; border: 1px solid #95a5a6; ">$<?php echo number_format($linea['total'],2) ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <br>
    <table width="100%" style="font-family: sans-serif; font-size: 14px;" >
        <tr>
            <td>
                <table width="70%" align="left" style="font-family: sans-serif; font-size: 14px;" >
                    <tr>
                        <td style="padding: 0px; line-height: 20px;">&nbsp;</td>
                    </tr>
                </table>
                <table width="30%" align="right" style="font-family: sans-serif; font-size: 14px; border-collapse: collapse;" >
                    <tr>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;"><strong>Sub-Total</strong></td>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;">$<?php echo number_format($sub_total,2)  ?></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;"><strong>IVA</strong></td>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;">$<?php echo number_format($iva,2); ?></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;"><strong>Total</strong></td>
                        <td style="border: 1px solid #95a5a6; padding: 10px 8px; line-height: 20px;">$<?php echo number_format($total,2)?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <div style="position: absolute; bottom: 0px; left: 90px;">
        <p style="text-align:center;">Real del Seminario 122, Valle del Real Celaya, Gto. 38024. RFC RERA7701272R1. ventas@sellopronto.com.mx</p>
        <p style="text-align:center;">La siguente orden de compra esta expresada en pesos Mexicanos</p>
    </div>
</body>
</html>