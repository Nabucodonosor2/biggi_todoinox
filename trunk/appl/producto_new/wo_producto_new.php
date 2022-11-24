<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('producto_new')) {
	$wo_producto = new wo_producto_new();
	$wo_producto->retrieve();
}
else {
	$wo = session::get('wo_producto_new');
	$wo->procesa_event();
}
?>