<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$nro_oc		 = $_REQUEST['nro_oc'];
$cod_empresa = $_REQUEST['cod_empresa'];
$cod_factura = $_REQUEST['cod_factura'];

if($nro_oc == '')
	 $nro_oc = 'NULL';
if($cod_empresa == '')
	 $nro_oc = 'NULL';	 
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if($cod_factura == ''){
	$sql = "SELECT COD_EMPRESA
			FROM FACTURA
			WHERE NRO_ORDEN_COMPRA = '$nro_oc'
			AND COD_EMPRESA = $cod_empresa";
}else{
	$sql = "SELECT COD_EMPRESA
			FROM FACTURA
			WHERE NRO_ORDEN_COMPRA = '$nro_oc'
			AND COD_EMPRESA = $cod_empresa
			AND COD_FACTURA <> $cod_factura";	
}		
$result = $db->build_results($sql);
if(count($result) == 0)	
	print 'NO_EXISTE';
else
	print 'EXISTE';
?>