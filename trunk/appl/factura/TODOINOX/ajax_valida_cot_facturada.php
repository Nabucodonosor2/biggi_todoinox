<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$cod_cotizacion = $_REQUEST['vl_cod_cotizacion'];

if($cod_cotizacion == '')
$cod_cotizacion = 0;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT COUNT(*) COUNT
		FROM FACTURA
		WHERE COD_COTIZACION = $cod_cotizacion";
$result = $db->build_results($sql);

if($result[0]['COUNT'] > 0){
	$sql_fa = "SELECT COUNT(*) COUNT
			   FROM FACTURA
			   WHERE COD_COTIZACION = $cod_cotizacion
			   AND COD_ESTADO_DOC_SII = 1";
	$result_fa = $db->build_results($sql_fa);
	if($result_fa[0]['COUNT'] == 0){
		
		$sql = "SELECT COUNT(*) COUNT
				FROM NOTA_CREDITO
				WHERE COD_DOC IN (SELECT COD_FACTURA
								  FROM FACTURA
								  WHERE COD_COTIZACION = $cod_cotizacion
								  AND COD_ESTADO_DOC_SII <> 4)";
		$result = $db->build_results($sql);

		if($result[0]['COUNT'] > 0){
			$sql = "SELECT COUNT(*) COUNT
					FROM NOTA_CREDITO
					WHERE COD_DOC IN (SELECT COD_FACTURA
									  FROM FACTURA
									  WHERE COD_COTIZACION = $cod_cotizacion
									  AND COD_ESTADO_DOC_SII <> 4)
					AND COD_ESTADO_DOC_SII = 1";
			$result = $db->build_results($sql);
			if($result[0]['COUNT'] > 0){
				print '2';
			}else{
				
				$sql = "SELECT SUM(TOTAL_CON_IVA) SUM_TOTAL_CON_IVA
			   			FROM FACTURA
			   			WHERE COD_COTIZACION = $cod_cotizacion
			   			AND COD_ESTADO_DOC_SII <> 4";
				$result = $db->build_results($sql);
				$SUM_TOTAL_CON_IVA_FA = $result[0]['SUM_TOTAL_CON_IVA'];
				
				$sql = "SELECT SUM(TOTAL_CON_IVA) SUM_TOTAL_CON_IVA
						FROM NOTA_CREDITO
						WHERE COD_DOC IN (SELECT COD_FACTURA
										  FROM FACTURA
										  WHERE COD_COTIZACION = $cod_cotizacion
										  AND COD_ESTADO_DOC_SII <> 4)
						AND COD_ESTADO_DOC_SII <> 4";
				$result = $db->build_results($sql);
				$SUM_TOTAL_CON_IVA_NC = $result[0]['SUM_TOTAL_CON_IVA'];
				
				if($SUM_TOTAL_CON_IVA_FA == $SUM_TOTAL_CON_IVA_NC)
					print '4';
				else{
					$sql = "SELECT NRO_FACTURA
						    FROM FACTURA
						    WHERE COD_COTIZACION = $cod_cotizacion
						    AND COD_ESTADO_DOC_SII <> 4";
					$result = $db->build_results($sql);	    
					
					for($i=0 ; $i < count($result) ; $i++)
						$nro_facturas .= $result[$i]['NRO_FACTURA'].",";
					
					$nro_facturas = trim($nro_facturas, ',');	
						
					print '3|'.$nro_facturas;
				}	
			}		
		}else{
			print 'x';
		}
	}else{
		print '1';
	}
}else
	print '0'; //no hay factura
?>