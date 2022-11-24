<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$cod_usuario = session::get("COD_USUARIO");
$puede_usar_precio_cot = w_base::tiene_privilegio_opcion_usuario('990505', $cod_usuario);
$cod_cotizacion = $_REQUEST['regreso'];
$num_dif = 0;
$respuesta = "";

$sql = "SELECT	COD_PRODUCTO,
				PRECIO
			FROM		ITEM_COTIZACION
			WHERE		COD_COTIZACION = $cod_cotizacion
			ORDER BY	ORDEN";
$result_i = $db->build_results($sql);
for ($i=0 ; $i <count($result_i); $i++) {
	$cod_producto = $result_i[$i]['COD_PRODUCTO'];
	$precio_cot = $result_i[$i]['PRECIO'];
	$result	= $db->build_results("select PRECIO_VENTA_PUBLICO
								, PRECIO_LIBRE from PRODUCTO where COD_PRODUCTO = '$cod_producto'");
	if ($result[0]['PRECIO_LIBRE']=='S') 
			continue;											  
	$precio_bd	= $result[0]['PRECIO_VENTA_PUBLICO'];
	if($precio_bd != $precio_cot ){
		$num_dif++;
		break;
	}
}
$sql="SELECT PORC_MODIFICA_PRECIO 
			  FROM USUARIO
			  WHERE COD_USUARIO = $cod_usuario";
$result = $db->build_results($sql);
if($result[0]['PORC_MODIFICA_PRECIO'] == 0){
	if($num_dif > 0){
		$respuesta = "$cod_cotizacion|NO";
	}	
}else{
	if($num_dif > 0)
		$respuesta = "$cod_cotizacion|SI";
}


print $respuesta;	

?>