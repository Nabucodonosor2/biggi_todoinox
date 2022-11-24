<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$temp = new Template_appl('dlg_crea_desde_oc.htm');
$sql = "SELECT 'N' CREAR_DESDE
				,'N' DUPLICAR
				,NULL ORDEN_COMPRA
				,0 CANTIDAD
				,'S' CREAR_DESDE_QUOTE
				,'none' DISPLAY";
$dw = new datawindow($sql);
$dw->add_control($control = new edit_radio_button('CREAR_DESDE_QUOTE', 'S', 'N', 'Create from Quote', 'RADIO'));
$control->set_onChange('display_td();');
$dw->add_control($control = new edit_radio_button('CREAR_DESDE', 'S', 'N', 'Duplicate Purchase Order', 'RADIO'));
$control->set_onChange('display_td();');
$dw->add_control($control = new edit_radio_button('DUPLICAR', 'S', 'N', 'Duplicate', 'RADIO'));
$control->set_onChange('display_td();');
$dw->add_control(new edit_text('ORDEN_COMPRA', 10, 10));
$dw->add_control(new edit_num('CANTIDAD', 2, 10));
$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();
?>