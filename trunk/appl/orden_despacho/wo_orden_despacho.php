<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if(w_output::f_viene_del_menu('orden_despacho')){
  $wo_orden_despacho = new wo_orden_despacho();
  $wo_orden_despacho->retrieve();
}else{
  $wo = session::get('wo_orden_despacho');
  $wo->procesa_event();
}
?>