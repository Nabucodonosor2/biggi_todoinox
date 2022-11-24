<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
$cod_usuario = session::get('COD_USUARIO');

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
  
$sql="SELECT PORC_MODIFICA_PRECIO 
	  FROM USUARIO
	  WHERE COD_USUARIO = $cod_usuario";
	 
$result = $db->build_results($sql);

print $result[0]['PORC_MODIFICA_PRECIO'];

?>