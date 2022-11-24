<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
ini_set('display_errors', 'off');
$correlative = $_REQUEST['correlative'];


$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT C.COD_CX_COT_EXTRANJERA	
				,P.ALIAS_PROVEEDOR_EXT
                ,P.NOM_PROVEEDOR_EXT
                ,CL.NOM_CX_CLAUSULA_COMPRA
                ,C.MONTO_TOTAL	              						                    
		FROM CX_COT_EXTRANJERA C
			,USUARIO U
			,PROVEEDOR_EXT P
			,CX_CONTACTO_PROVEEDOR_EXT CC
			,CX_CLAUSULA_COMPRA CL
			,CX_ESTADO_COT_EXTRANJERA CE
		WHERE CC.COD_CX_CONTACTO_PROVEEDOR_EXT= C.COD_CX_CONTACTO_PROVEEDOR_EXT
		  AND U.COD_USUARIO = C.COD_USUARIO
		  AND P.COD_PROVEEDOR_EXT = C.COD_PROVEEDOR_EXT
		  AND C.COD_CX_CLAUSULA_COMPRA = CL.COD_CX_CLAUSULA_COMPRA
		  AND C.COD_CX_ESTADO_COT_EXTRANJERA = CE.COD_CX_ESTADO_COT_EXTRANJERA
          AND C.COD_CX_COT_EXTRANJERA =$correlative
		ORDER BY C.COD_CX_COT_EXTRANJERA DESC";
$result = $db->build_results($sql);  

for ($i = 0; $i < count($result); $i++){
 	$result[$i]['COD_CX_COT_EXTRANJERA'] = urlencode($result[$i]['COD_CX_COT_EXTRANJERA']);	
	$result[$i]['ALIAS_PROVEEDOR_EXT'] = urlencode($result[$i]['ALIAS_PROVEEDOR_EXT']);
	$result[$i]['NOM_PROVEEDOR_EXT'] = urlencode($result[$i]['NOM_PROVEEDOR_EXT']);
	$result[$i]['NOM_CX_CLAUSULA_COMPRA'] = urlencode($result[$i]['NOM_CX_CLAUSULA_COMPRA']);
	$result[$i]['MONTO_TOTAL'] = urlencode($result[$i]['MONTO_TOTAL']);
}
if(count($result)==0){
    print $result='';
}else{
    print json_encode($result);
}


?>