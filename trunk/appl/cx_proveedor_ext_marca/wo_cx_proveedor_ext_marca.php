<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cx_proveedor_ext_marca')) {
	$wo_cx_proveedor_ext_marca = new wo_cx_proveedor_ext_marca();
	$wo_cx_proveedor_ext_marca->retrieve();
}
else {
	$wo = session::get('wo_cx_proveedor_ext_marca');
	$wo->procesa_event();	
}
?>