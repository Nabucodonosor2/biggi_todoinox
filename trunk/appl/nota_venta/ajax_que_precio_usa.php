<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$cod_usuario = session::get("COD_USUARIO");
$cod_nota_venta = $_REQUEST['regreso'];
$num_dif = 0;
$respuesta = "";

$sql = "select 		COD_PRODUCTO,
					PRECIO
				from ITEM_NOTA_VENTA
				where COD_NOTA_VENTA = $cod_nota_venta
				order by ORDEN asc";
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
	}
}
if($num_dif > 0)
	$respuesta = "$cod_nota_venta|SI";
else
	$respuesta = "$cod_nota_venta|NO";


print $respuesta;	

?>