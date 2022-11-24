<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
require_once("class_print_reporte_comercial.php");
require_once("class_print_reporte_bodega.php");
$sistema			= $_REQUEST['sistema'];
$cod_orden_compra	= $_REQUEST['cod_orden_compra'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT SISTEMA
			  ,URL_WS
			  ,USER_WS
			  ,PASSWROD_WS
		FROM PARAMETRO_WS
		WHERE SISTEMA = '$sistema'";
$result = $db->build_results($sql);

$user_ws		= $result[0]['USER_WS'];
$passwrod_ws	= $result[0]['PASSWROD_WS'];
$url_ws			= $result[0]['URL_WS'];

$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
$sql = $biggi->cli_print_oc_sistema($cod_orden_compra, $sistema);

$result_oc = $db->build_results($sql);

$labels = array();
$labels['strCOD_ORDEN_COMPRA'] = $cod_orden_compra;
$labels['strFECHA_ORDEN_COMPRA'] = $result_oc[0]['FECHA_ORDEN_COMPRA'];
if($sistema == 'COMERCIAL'){
	$rpt = new print_reporte_comercial($sql, K_ROOT_DIR.'appl/orden_compra/orden_compra.xml', $labels, "Orden de Compra ".$cod_orden_compra, 1, true);
}else if($sistema == 'BODEGA')
	$rpt = new print_reporte_bodega($sql, K_ROOT_DIR.'appl/orden_compra/orden_compra.xml', $labels, "Orden de Compra ".$cod_orden_compra, 1, true);
//echo $sql['ORDEN_COMPRA_SERV']
?>