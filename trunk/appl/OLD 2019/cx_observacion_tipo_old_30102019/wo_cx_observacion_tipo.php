<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_observacion_tipo')) {
	$wo_cx_observacion_tipo = new wo_cx_observacion_tipo();
	$wo_cx_observacion_tipo->retrieve();
}
else {
	$wo = session::get('wo_cx_observacion_tipo');
	$wo->procesa_event();	
}
?>