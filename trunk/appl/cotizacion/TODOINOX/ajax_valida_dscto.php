<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_cotizacion = $_REQUEST['cod_cotizacion'];
$cod_usuario = session::get("COD_USUARIO");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
  
$sql="select PORC_DESCUENTO_PERMITIDO
	  from USUARIO
	  where COD_USUARIO = $cod_usuario";

$result = $db->build_results($sql);
$porc_descuento_permitido = $result[0]['PORC_DESCUENTO_PERMITIDO'];

if($cod_cotizacion == '')
	$descuento_bd = 0;
else{
	$sql="SELECT PORC_DSCTO1
		  FROM COTIZACION
		  WHERE COD_COTIZACION = $cod_cotizacion";

	$result = $db->build_results($sql);
	$descuento_bd = $result[0]['PORC_DSCTO1'];
}	

$respuesta = $porc_descuento_permitido.'|'.$descuento_bd;	
print $respuesta;
?>