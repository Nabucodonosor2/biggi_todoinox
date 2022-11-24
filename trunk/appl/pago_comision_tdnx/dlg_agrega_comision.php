<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_agrega_comision.htm');

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT TOP 1 CONVERT(VARCHAR, FECHA_HASTA+1, 103) FECHA_HASTA
		FROM PAGO_COMISION_TDNX
		ORDER BY COD_PAGO_COMISION_TDNX DESC";
$result = $db->build_results($sql);

$sql = "SELECT '".$result[0]['FECHA_HASTA']."' FECHA_DESDE
			  ,CONVERT(VARCHAR, GETDATE(), 103) FECHA_HASTA
			  ,'".$result[0]['FECHA_HASTA']."' FECHA_DESDE_H
			  ,CONVERT(VARCHAR, GETDATE(), 103) FECHA_HASTA_H";

$dw = new datawindow($sql);
$dw->add_control(new edit_date('FECHA_DESDE'));
$dw->add_control(new edit_date('FECHA_HASTA'));
$dw->add_control(new edit_text_hidden('FECHA_DESDE_H'));
$dw->add_control(new edit_text_hidden('FECHA_HASTA_H'));

$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();	
?>