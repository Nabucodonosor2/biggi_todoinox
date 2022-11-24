<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$nro_ri = $_REQUEST['nro_ri'];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT count(*) CANT
		FROM REGISTRO_INGRESO_4D 
		WHERE NUMERO_REGISTRO_INGRESO =$nro_ri";
$result = $db->build_results($sql);
if ($result[0]['CANT']==0) {
	print 'NO_EXISTE|0';
	return;
}
		
$sql = "SELECT IMPORTADO_DESDE_4D
		FROM REGISTRO_INGRESO_4D 
		WHERE NUMERO_REGISTRO_INGRESO =$nro_ri";
$result = $db->build_results($sql);
if ($result[0]['IMPORTADO_DESDE_4D']=='S') {
	print 'RI_4D|0';
	return;
}
		
$sql = "SELECT COD_ENTRADA_BODEGA
		FROM ENTRADA_BODEGA
		WHERE TIPO_DOC ='REGISTRO_INGRESO'
		  and COD_DOC = $nro_ri";
$result = $db->build_results($sql);
if (count($result) > 0) {
	$cod_entrada_bodega = $result[0]['COD_ENTRADA_BODEGA'];
	print "ENTRADA_YA_EXISTE|$cod_entrada_bodega";
	return;
}

print 'OK|0';
?>