<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$cod_contacto_persona = $_REQUEST["cod_contacto_persona"];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT COD_CONTACTO_PERSONA, CARGO, NOM_PERSONA
		FROM CONTACTO_PERSONA
		WHERE COD_CONTACTO_PERSONA =".$cod_contacto_persona;
$result = $db->build_results($sql);
print urlencode(json_encode($result));
?>