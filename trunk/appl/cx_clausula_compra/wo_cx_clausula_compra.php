<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_clausula_compra')) {
	$wo_cx_clausula_compra = new wo_cx_clausula_compra();
	$wo_cx_clausula_compra->retrieve();
}
else {
	$wo = session::get('wo_cx_clausula_compra');
	$wo->procesa_event();	
}
?>