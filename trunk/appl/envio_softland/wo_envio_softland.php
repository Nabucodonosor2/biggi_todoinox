<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('envio_softland')) {
  $wo_envio_softland = new wo_envio_softland();
  $wo_envio_softland->retrieve();
} 
else {
  $wo = session::get('wo_envio_softland');
  $wo->procesa_event();
}
?>