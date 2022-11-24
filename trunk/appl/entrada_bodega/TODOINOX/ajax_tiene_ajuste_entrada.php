<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_producto = $_REQUEST['cod_producto'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT TOP 1 EB.COD_ENTRADA_BODEGA
					,CONVERT(VARCHAR, EB.FECHA_ENTRADA_BODEGA, 103) FECHA_ENTRADA_BODEGA_S
					,EB.FECHA_ENTRADA_BODEGA
					,(SELECT NOM_USUARIO FROM USUARIO WHERE COD_USUARIO = EB.COD_USUARIO) NOM_USUARIO
		FROM ENTRADA_BODEGA EB
			,ITEM_ENTRADA_BODEGA IEB
		WHERE IEB.COD_PRODUCTO = '$cod_producto'
		AND EB.TIPO_DOC = 'AJUSTE' 
		AND EB.COD_ENTRADA_BODEGA = IEB.COD_ENTRADA_BODEGA
		ORDER BY EB.FECHA_ENTRADA_BODEGA DESC";

$result = $db->build_results($sql);

$sql2 = "SELECT TOP 1 SB.COD_SALIDA_BODEGA
					,CONVERT(VARCHAR, SB.FECHA_SALIDA_BODEGA, 103) FECHA_SALIDA_BODEGA_S
					,SB.FECHA_SALIDA_BODEGA
					,(SELECT NOM_USUARIO FROM USUARIO WHERE COD_USUARIO = SB.COD_USUARIO) NOM_USUARIO
		FROM SALIDA_BODEGA SB
			,ITEM_SALIDA_BODEGA ISB
		WHERE ISB.COD_PRODUCTO = '$cod_producto'
		AND SB.TIPO_DOC = 'AJUSTE' 
		AND SB.COD_SALIDA_BODEGA = ISB.COD_SALIDA_BODEGA
		ORDER BY SB.FECHA_SALIDA_BODEGA DESC";

$result2 = $db->build_results($sql2);

if(count($result) == 1 && count($result2) == 0){
	print 'TIENE_AJUSTE|'.$result[0]['FECHA_ENTRADA_BODEGA_S'].'|'.$result[0]['NOM_USUARIO'].'|ENTRADA|'.$result[0]['COD_ENTRADA_BODEGA'];
}else if(count($result) == 0 && count($result2) == 1){
	print 'TIENE_AJUSTE|'.$result2[0]['FECHA_SALIDA_BODEGA_S'].'|'.$result2[0]['NOM_USUARIO'].'|SALIDA|'.$result[0]['COD_SALIDA_BODEGA'];
}else if(count($result) == 1 && count($result2) == 1){
	
	$sql3="SELECT CASE
					WHEN CONVERT(DATETIME, '".$result[0]['FECHA_ENTRADA_BODEGA']."') > CONVERT(DATETIME, '".$result2[0]['FECHA_SALIDA_BODEGA']."') THEN 'MAYOR'
					ELSE 'MENOR'
	   	   END FECHAS";
	
	$result3 = $db->build_results($sql3);
	
	if($result3[0]['FECHAS'] == 'MAYOR'){
		print 'TIENE_AJUSTE|'.$result[0]['FECHA_ENTRADA_BODEGA_S'].'|'.$result[0]['NOM_USUARIO'].'|ENTRADA|'.$result[0]['COD_ENTRADA_BODEGA'];
	}else{
		print 'TIENE_AJUSTE|'.$result2[0]['FECHA_SALIDA_BODEGA_S'].'|'.$result2[0]['NOM_USUARIO'].'|SALIDA|'.$result[0]['COD_SALIDA_BODEGA'];
	}			
}else{
	print '';
}
?>