<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$array_fechas = $_REQUEST['fechas'];
$array = explode(',', $array_fechas);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
function str2date($fecha_str, $hora_str='00:00:00') {
	if ($fecha_str=='')
		return 'null';
	// Entra la fecha en formato dd/mm/yyyy		
	if (K_TIPO_BD=='mssql') {
		$res = explode('/', $fecha_str);
		if (strlen($res[2])==2)
			$res[2] = '20'.$res[2];
		return sprintf("{ts '$res[2]-$res[1]-$res[0] $hora_str.000'}");
	}
	else if (K_TIPO_BD=='oci')
		return "to_date('$fecha_str $hora_str', 'dd/mm/yyyy hh24:mi:ss')";
	else
		base::error("base.str2date, no soportado para ".K_TIPO_BD);
}

$vl_fecha_desde = str2date($array[0]);
$vl_fecha_hasta = str2date($array[1]);
$vl_fecha_desde_h = str2date($array[2]);
$vl_fecha_hasta_h = str2date($array[3]);

$sql = "SELECT CASE
				WHEN $vl_fecha_desde < $vl_fecha_desde_h THEN 'ALERTA1'
				ELSE ''
			   END VALIDACION1";
$result = $db->build_results($sql);

if($result[0]['VALIDACION1'] == 'ALERTA1'){
	print 'ALERTA1';
	return;
}	

$sql = "SELECT CASE
				WHEN $vl_fecha_hasta < $vl_fecha_desde THEN 'ALERTA2'
				ELSE ''
			   END VALIDACION2";
$result = $db->build_results($sql);

if($result[0]['VALIDACION2'] == 'ALERTA2'){
	print 'ALERTA2';
	return;
}	
	
$sql = "SELECT CASE
				WHEN $vl_fecha_hasta > $vl_fecha_hasta_h THEN 'ALERTA3'
				ELSE ''
			   END VALIDACION3";
$result = $db->build_results($sql);

if($result[0]['VALIDACION3'] == 'ALERTA3'){
	print 'ALERTA3';
	return;
}	
?>