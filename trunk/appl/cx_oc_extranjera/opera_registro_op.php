<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

function str2date($fecha_str, $hora_str='00:00:00') {
	if ($fecha_str=='')
		return 'null';
	// Entra la fecha en formato dd/mm/yyyy
	$res = explode('/', $fecha_str);
	if (strlen($res[2])==2)
		$res[2] = '20'.$res[2];
	return sprintf("{ts '$res[2]-$res[1]-$res[0] $hora_str.000'}");
}

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$vl_values = explode("|", $_REQUEST['values']);

$cod_cx_oc_extranjera	= $vl_values[0];
$fecha_carta_op			= str2date($vl_values[1]);
$porc_pago				= str_replace(",", ".", $vl_values[2]);
$monto_pago				= str_replace(",", ".", $vl_values[3]);
$cod_cx_carta_op		= $vl_values[4];
$cod_estado_carta_op	= $vl_values[5];
$atencion_carta			= $vl_values[6];

$sp = "spu_cx_orden_pago";

if($cod_cx_carta_op == "")
	$operacion = "'INSERT'";
else
	$operacion = "'UPDATE'";

$cod_cx_carta_op	= ($cod_cx_carta_op =='') ? "null" : "$cod_cx_carta_op";
$atencion_carta		= ($atencion_carta =='') ? "null" : "'$atencion_carta'";	
	
$param = "$operacion
		 ,$cod_cx_carta_op
		 ,$cod_cx_oc_extranjera
		 ,$fecha_carta_op
		 ,$porc_pago
		 ,$monto_pago
		 ,$cod_estado_carta_op
		 ,null
		 ,null
		 ,null
		 ,$atencion_carta";

if($db->EXECUTE_SP($sp, $param))
	print 'exito';
else
	print 'null';	
?>