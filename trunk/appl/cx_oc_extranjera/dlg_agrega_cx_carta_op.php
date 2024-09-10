<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_agrega_cx_carta_op.htm');
$cod_cx_oc_extranjera = $_REQUEST['cx_co_extranjera'];
$cod_cx_carta_op = $_REQUEST['cod_cx_carta_op'];
$estado = $_REQUEST['estado'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_1 = "SELECT ISNULL(SUM(MONTO_PAGO), 0) SUM_MONTO_PAGO
		  FROM CX_CARTA_OP C
		  WHERE COD_CX_OC_EXTRANJERA = $cod_cx_oc_extranjera
		  AND C.COD_ESTADO_CX_CARTA_OP = 3";
$result_1 = $db->build_results($sql_1);

$sql_2 = "SELECT MONTO_TOTAL
		  FROM CX_OC_EXTRANJERA
		  WHERE COD_CX_OC_EXTRANJERA = $cod_cx_oc_extranjera";
$result_2 = $db->build_results($sql_2);

$sum_monto_pago = $result_1[0]['SUM_MONTO_PAGO'];
$monto_total	= $result_2[0]['MONTO_TOTAL'];

if($cod_cx_carta_op == '')
	$sql = "SELECT CONVERT(VARCHAR, GETDATE(), 103) FECHA_CARTA_OP
				  ,0 PORC_PAGO
				  ,0 MONTO_PAGO
				  ,$cod_cx_oc_extranjera COD_CX_OC_EXTRANJERA
				  ,NULL COD_CX_CARTA_OP
				  ,1 COD_ESTADO_CX_CARTA_OP
				  ,'$estado' ESTADO
				  ,$sum_monto_pago SUM_MONTO_PAGO
				  ,$monto_total MONTO_TOTAL
				  ,dbo.f_get_parametro(76) ATENCION_CARTA";
else
	$sql = "SELECT CONVERT(VARCHAR, GETDATE(), 103) FECHA_CARTA_OP
				  ,PORC_PAGO
				  ,MONTO_PAGO
				  ,COD_CX_OC_EXTRANJERA
				  ,COD_CX_CARTA_OP
				  ,COD_ESTADO_CX_CARTA_OP
				  ,'$estado' ESTADO
				  ,$sum_monto_pago SUM_MONTO_PAGO
				  ,$monto_total MONTO_TOTAL
				  ,ATENCION_CARTA
			FROM CX_CARTA_OP
			WHERE COD_CX_CARTA_OP =	$cod_cx_carta_op";	  

$dw = new datawindow($sql);
$dw->add_control(new edit_text_hidden('COD_CX_OC_EXTRANJERA'));
$dw->add_control(new edit_text_hidden('COD_CX_CARTA_OP'));
$dw->add_control(new edit_text_hidden('SUM_MONTO_PAGO'));
$dw->add_control(new edit_text_hidden('MONTO_TOTAL'));
$dw->add_control(new edit_date('FECHA_CARTA_OP'));
$dw->add_control(new edit_porcentaje('PORC_PAGO'));
$dw->add_control(new edit_num('MONTO_PAGO', 10, 10, 2));
$dw->add_control(new edit_text('ATENCION_CARTA', 30, 100));
$sql="SELECT COD_ESTADO_CX_CARTA_OP
			,NOM_ESTADO_CX_CARTA_OP
	  FROM ESTADO_CX_CARTA_OP
	  WHERE COD_ESTADO_CX_CARTA_OP <> 2";	
$dw->add_control(new drop_down_dw('COD_ESTADO_CX_CARTA_OP', $sql, 150));

$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();
?>