<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_puerto_arribo')) {
	$wo_cx_puerto_arribo = new wo_cx_puerto_arribo();
	$wo_cx_puerto_arribo->retrieve();
}
else {
	$wo = session::get('wo_cx_puerto_arribo');
	$wo->procesa_event();	
}
?>