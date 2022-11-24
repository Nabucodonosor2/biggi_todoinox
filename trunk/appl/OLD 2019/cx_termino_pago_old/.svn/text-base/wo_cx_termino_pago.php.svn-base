<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_termino_pago')) {
	$wo_cx_termino_pago = new wo_cx_termino_pago();
	$wo_cx_termino_pago->retrieve();
}
else {
	$wo = session::get('wo_cx_termino_pago');
	$wo->procesa_event();	
}
?>