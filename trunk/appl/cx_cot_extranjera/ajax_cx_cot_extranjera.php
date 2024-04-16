<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$fx = $_REQUEST['fx'];
$variable1 = $_REQUEST['variable1'];
$variable2 = $_REQUEST['variable2'];
$print = "";


if ($fx == 'get_provedor'){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    $sql = "SELECT TELEFONO
                ,MAIL
            FROM CX_CONTACTO_PROVEEDOR_EXT
            WHERE COD_PROVEEDOR_EXT = $variable1
            AND COD_CX_CONTACTO_PROVEEDOR_EXT = $variable2
            ORDER BY COD_CX_CONTACTO_PROVEEDOR_EXT";

	$result = $db->build_results($sql);
    if($result > 0){
        $print = $result[0]['TELEFONO'].'|'.$result[0]['MAIL'];
    }else{
        $print = 'NULL';
    }
}

print $print
?>