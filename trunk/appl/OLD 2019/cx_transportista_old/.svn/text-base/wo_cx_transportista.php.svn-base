<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_transportista')) {
	$wo_cx_transportista = new wo_cx_transportista();
	$wo_cx_transportista->retrieve();
}
else {
	$wo = session::get('wo_cx_transportista');
	$wo->procesa_event();	
}
?>