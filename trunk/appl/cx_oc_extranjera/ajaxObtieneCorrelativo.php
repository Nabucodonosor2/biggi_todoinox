<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$cod_empresa = URLDecode($_REQUEST['cod_empresa']);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);


$sql = "SELECT ALIAS_PROVEEDOR_EXT,YEAR(GETDATE())ANO  FROM PROVEEDOR_EXT where COD_PROVEEDOR_EXT = $cod_empresa";
$result = $db->build_results($sql);

$ANO = $result[0]['ANO'];
$ALIAS = $result[0]['ALIAS_PROVEEDOR_EXT'];

$sql="select TOP 1 CX.CORRELATIVO_OC
        from CX_OC_EXTRANJERA CX 
        where CX.COD_PROVEEDOR_EXT = $cod_empresa
        ORDER BY CX.COD_CX_OC_EXTRANJERA DESC";	
$result = $db->build_results($sql);

$CORRELATIVO_STR = $result[0]['CORRELATIVO_OC'];

if(!empty($CORRELATIVO_STR)){
    $arrOld = explode( '/', $CORRELATIVO_STR );
    $str = $arrOld[0];
    $anoOLd = $arrOld[1];
    
    $correlativo = substr($str, -2);
    $correlativo = trim($correlativo);
    $n_correlativo = (int)$correlativo + 1;
    if(trim($anoOLd) != $ANO){
        $n_correlativo = 1;
    }
}else{
    $n_correlativo = 1;
}

$correlativo_new = $ALIAS.' '.$n_correlativo.'/'.$ANO;
print $correlativo_new;
?>