<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('pago_comision_tdnx')) {
	$wo_pago_comision_tdnx = new wo_pago_comision_tdnx();
	$wo_pago_comision_tdnx->retrieve();
}
else {
	$wo = session::get('wo_pago_comision_tdnx');
	$wo->procesa_event();
}
?>