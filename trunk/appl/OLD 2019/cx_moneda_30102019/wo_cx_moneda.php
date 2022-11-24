<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_moneda')) {
	$wo_cx_moneda = new wo_cx_moneda();
	$wo_cx_moneda->retrieve();
}
else {
	$wo = session::get('wo_cx_moneda');
	$wo->procesa_event();	
}
?>