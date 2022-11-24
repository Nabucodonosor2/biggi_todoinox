<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_cot_extranjera')) {

	$wo_cx_cot_extranjera = new wo_cx_cot_extranjera();
	$wo_cx_cot_extranjera->retrieve();
} 
else {
	$wo = session::get('wo_cx_cot_extranjera');
	$wo->procesa_event();
}
?>