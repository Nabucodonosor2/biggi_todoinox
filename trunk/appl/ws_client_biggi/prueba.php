<?php
require("class_client_biggi.php");
//192.168.2.140/desarrolladores/icampos/biggi/trunk/appl/ws_client_biggi/prueba.php
$biggi = new client_biggi("ws_biggi", "2821", "http://190.96.2.187/sysbiggi_new/biggi_bodega/trunk/appl/ws_server_biggi/server_biggi.php");
$res = $biggi->cli_orden_compra("50874");
print_r($res);
?>