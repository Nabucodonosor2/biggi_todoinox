<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('carta_compra_usd')) {
	$wo_carta_compra_usd = new wo_carta_compra_usd();
	$wo_carta_compra_usd->retrieve();
}
else {	
	$wo = session::get('wo_carta_compra_usd');
	$wo->procesa_event();
	
}
?>