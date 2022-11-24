<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_puerto_salida')) {
	$wo_cx_puerto_salida = new wo_cx_puerto_salida();
	$wo_cx_puerto_salida->retrieve();
}
else {
	$wo = session::get('wo_cx_puerto_salida');
	$wo->procesa_event();	
}
?>