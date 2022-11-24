<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$str_cod_carta_op = $_REQUEST['str_cod_carta_op'];
$str_cod_carta_op = explode(",", $str_cod_carta_op);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

for($i=0 ; $i < count($str_cod_carta_op) ; $i++){
	$sp = "spu_cx_orden_pago";
	$param = "'ANULA'
			 ,".$str_cod_carta_op[$i];
	
	$db->EXECUTE_SP($sp, $param);
}

print '';
?>