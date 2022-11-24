<?php
require_once(dirname(__FILE__)."/../ws_client_biggi/class_client_biggi.php");
	
$nom_tabla = $_POST['TABLA'];
$cod_tabla = $_POST['COD_TABLA'];

$biggi = new client_biggi("ws_biggi", "2821", "http://www.biggi.cl/sysbiggi_new/bodega_biggi/biggi/trunk/appl/ws_server_biggi/server_biggi.php");
$result = $biggi->cli_tabla($nom_tabla, $cod_tabla);

if($result == 'NO_REGISTRO'){
	echo '<script type="text/javascript">
          	alert("Tabla o Código no existente");
		  </script>';
}else{
	$array = array_keys($result['TABLA'][0]);
	
	for($i=0; $i < count($array) ; $i++)
		echo $array[$i].' => '.$result['TABLA'][0][$array[$i]].'<br><br>';
}
	
?>