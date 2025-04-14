<?php

function moneda_mxn($cantidad)
{
    return 'MXN $' . number_format($cantidad, 2);
}
