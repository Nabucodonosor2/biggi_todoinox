<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_doc = $_REQUEST["cod_doc"];
$cod_doc = base64_decode($cod_doc);
$cod_factura = $_REQUEST["cod_factura"];
$nom_factura = $_REQUEST["factura"];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if($nom_factura == 'factura'){
	$sql = "SELECT FD.RUTA_ARCHIVO
				   ,FD.NOM_ARCHIVO
			FROM FACTURA_DOCS FD
			WHERE FD.ES_OC = 'S'
			AND FD.COD_FACTURA = $cod_factura";
}else{
	$sql = "SELECT FD.RUTA_ARCHIVO
				   ,FD.NOM_ARCHIVO
			FROM FACTURA_DOCS FD
			WHERE FD.COD_FACTURA_DOCS = $cod_doc"; 
}
$result = $db->build_results($sql);
$nom_archivo = $result[0]['NOM_ARCHIVO'];
$archivo = $result[0]['RUTA_ARCHIVO'].$nom_archivo;
if (file_exists($archivo)) {
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$nom_archivo\"\n");
	$fp=fopen("$archivo", "r");
	fpassthru($fp);
}
else
	base::alert("El archivo no se encontro!!");
?>