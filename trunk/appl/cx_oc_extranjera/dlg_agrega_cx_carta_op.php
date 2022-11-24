<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_agrega_cx_carta_op.htm');
$cod_cx_oc_extranjera = $_REQUEST['cx_co_extranjera'];
$cod_cx_carta_op = $_REQUEST['cod_cx_carta_op'];

if($cod_cx_carta_op == '')
	$sql = "SELECT CONVERT(VARCHAR, GETDATE(), 103) FECHA_CARTA_OP
				  ,0 PORC_PAGO
				  ,0 MONTO_PAGO
				  ,$cod_cx_oc_extranjera COD_CX_OC_EXTRANJERA
				  ,NULL COD_CX_CARTA_OP
				  ,1 COD_ESTADO_CX_CARTA_OP";
else
	$sql = "SELECT CONVERT(VARCHAR, GETDATE(), 103) FECHA_CARTA_OP
				  ,PORC_PAGO
				  ,MONTO_PAGO
				  ,COD_CX_OC_EXTRANJERA
				  ,COD_CX_CARTA_OP
				  ,COD_ESTADO_CX_CARTA_OP
			FROM CX_CARTA_OP
			WHERE COD_CX_CARTA_OP =	$cod_cx_carta_op";	  

$dw = new datawindow($sql);
$dw->add_control(new edit_text_hidden('COD_CX_OC_EXTRANJERA'));
$dw->add_control(new edit_text_hidden('COD_CX_CARTA_OP'));
$dw->add_control(new edit_date('FECHA_CARTA_OP'));
$dw->add_control(new edit_porcentaje('PORC_PAGO'));
$dw->add_control(new edit_num('MONTO_PAGO', 10, 10, 2));
$sql="SELECT COD_ESTADO_CX_CARTA_OP
			,NOM_ESTADO_CX_CARTA_OP
	  FROM ESTADO_CX_CARTA_OP";	
$dw->add_control(new drop_down_dw('COD_ESTADO_CX_CARTA_OP', $sql, 150));

$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();
?>