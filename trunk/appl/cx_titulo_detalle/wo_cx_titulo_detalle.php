<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_titulo_detalle')) {
	$wo_cx_titulo_detalle = new wo_cx_titulo_detalle();
	$wo_cx_titulo_detalle->retrieve();
}
else {
	$wo = session::get('wo_cx_titulo_detalle');
	$wo->procesa_event();	
}
?>