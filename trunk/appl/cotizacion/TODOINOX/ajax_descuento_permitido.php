<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_empresa = $_REQUEST['cod_empresa'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
  
$sql="SELECT DSCTO_PERMITIDO 
  	  FROM EMPRESA 
  	  WHERE COD_EMPRESA = $cod_empresa";
 
$result = $db->build_results($sql);
print $result[0]['DSCTO_PERMITIDO'];

?>