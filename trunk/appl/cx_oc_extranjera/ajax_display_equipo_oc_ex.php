<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$cod_producto = URLDecode($_REQUEST['cod_producto']);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$sql="SELECT COD_EQUIPO_OC_EX
				,DESC_EQUIPO_OC_EX 
		  FROM PRODUCTO
		  WHERE COD_PRODUCTO = '$cod_producto'";	
	
	$result = $db->build_results($sql);
	$result[0]['DESC_EQUIPO_OC_EX'] = urlencode($result[0]['DESC_EQUIPO_OC_EX']);
	
print urlencode(json_encode($result));
?>