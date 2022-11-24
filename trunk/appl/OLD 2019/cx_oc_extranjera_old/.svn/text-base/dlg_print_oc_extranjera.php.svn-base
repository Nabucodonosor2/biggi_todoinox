<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_print_oc_extranjera.htm');
$sql = "SELECT 'S' ORDEN_COMPRA
				,'' ORDEN_PAGO
				,'' INS_COBERTURA
				,'' ORDEN_CARGA";
$dw = new datawindow($sql);
$dw->add_control(new edit_radio_button('ORDEN_COMPRA', 'S', 'N', 'Purchase Order', 'RADIO'));
$dw->add_control(new edit_radio_button('ORDEN_PAGO', 'S', 'N', 'Carta Orden de Pago', 'RADIO'));
$dw->add_control(new edit_radio_button('INS_COBERTURA', 'S', 'N', 'Carta Instruccion de cobertura', 'RADIO'));
$dw->add_control(new edit_radio_button('ORDEN_CARGA', 'S', 'N', 'Carta Orden de Carga', 'RADIO'));
$dw->retrieve();
$dw->habilitar($temp, true);	
print $temp->toString();
?>