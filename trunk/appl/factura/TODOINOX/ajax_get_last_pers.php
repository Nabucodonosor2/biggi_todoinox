<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_empresa = $_REQUEST['cod_empresa'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql="SELECT TOP 1 COD_PERSONA
      FROM FACTURA
      WHERE COD_EMPRESA = $cod_empresa
      ORDER BY COD_FACTURA DESC";

$result = $db->build_results($sql);
$cod_persona = $result[0]['COD_PERSONA'];

print $cod_persona;
?>