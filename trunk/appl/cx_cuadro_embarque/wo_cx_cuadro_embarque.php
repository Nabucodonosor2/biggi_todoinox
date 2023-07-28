<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if(w_output::f_viene_del_menu('cx_cuadro_embarque')) {
	$wo_cx_cuadro_embarque = new wo_cx_cuadro_embarque();
	$wo_cx_cuadro_embarque->retrieve();
}else{
	$wo = session::get('wo_cx_cuadro_embarque');
	$wo->procesa_event();	
}
?>