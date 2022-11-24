<?php
// ini_set('display_errors', 1);
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/class_PHPMailer.php");
include(dirname(__FILE__)."/../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('COD_USUARIO', 1);

ini_set('max_execution_time', 900); //900 seconds = 15 minutes 

$temp = new Template_appl('facturas_por_cobrar_todoinox.htm');

$dws['FECHA'] = new datawindow("select convert(varchar, getdate(), 103) FECHA_INFORME");
$dws['FECHA']->retrieve();
$dws['FECHA']->habilitar($temp, false);

$dws['RESUMEN'] = new datawindow("exec spdw_fa_x_cobrar 'RESUMEN'", 'RESUMEN');
$dws['RESUMEN']->add_control(new static_num('MAS_90_TOTAL'));
$dws['RESUMEN']->add_control(new static_num('MAS_60_TOTAL'));
$dws['RESUMEN']->add_control(new static_num('MAS_30_TOTAL'));
$dws['RESUMEN']->add_control(new static_num('MENOS_30_TOTAL'));
$dws['RESUMEN']->add_control(new static_num('TOTAL'));
$dws['RESUMEN']->retrieve();
$dws['RESUMEN']->habilitar($temp, false);

$dws['OTROS_ANTIGUAS'] = new datawindow("exec spdw_fa_x_cobrar 'OTROS_ANTIGUAS'", 'OTROS_ANTIGUAS');
$dws['OTROS_ANTIGUAS']->add_control(new static_num('MONTO'));
$dws['OTROS_ANTIGUAS']->add_control(new static_num('PORC'));
$dws['OTROS_ANTIGUAS']->retrieve();
$dws['OTROS_ANTIGUAS']->habilitar($temp, false);

$dws['OTROS_DETALLE'] = new datawindow("exec spdw_fa_x_cobrar 'OTROS_DETALLE'", 'OTROS_DETALLE');
$dws['OTROS_DETALLE']->add_control(new static_num('MONTO'));
$dws['OTROS_DETALLE']->add_control(new static_num('PORC'));
$dws['OTROS_DETALLE']->retrieve();
$dws['OTROS_DETALLE']->habilitar($temp, false);

$dws['ARRIENDO_DETALLE'] = new datawindow("exec spdw_fa_x_cobrar 'ARRIENDO_DETALLE'", 'ARRIENDO_DETALLE');
$dws['ARRIENDO_DETALLE']->add_control(new static_num('MONTO'));
$dws['ARRIENDO_DETALLE']->add_control(new static_num('PORC'));
$dws['ARRIENDO_DETALLE']->retrieve();
$dws['ARRIENDO_DETALLE']->habilitar($temp, false);

$dws['SERVINDUS_ANTIGUAS'] = new datawindow("exec spdw_fa_x_cobrar 'SERVINDUS_ANTIGUAS'", 'SERVINDUS_ANTIGUAS');
$dws['SERVINDUS_ANTIGUAS']->add_control(new static_num('MONTO'));
$dws['SERVINDUS_ANTIGUAS']->add_control(new static_num('PORC'));
$dws['SERVINDUS_ANTIGUAS']->retrieve();
$dws['SERVINDUS_ANTIGUAS']->habilitar($temp, false);


print $temp->toString();
		
$html = $temp->toString();
$asunto = ' Facturas por cobrar TODOINOX';
	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "select dbo.f_get_parametro(53) 		URL_SMTP
		,dbo.f_get_parametro(54) 	USER_SMTP
		,dbo.f_get_parametro(55) 	PASS_SMTP
		,dbo.f_get_parametro(71) 	PORT_SMTP";
$result = $db->build_results($sql);

$host     = $result[0]['URL_SMTP'];
$Username = $result[0]['USER_SMTP'];
$Password = $result[0]['PASS_SMTP'];
$Port 	  = $result[0]['PORT_SMTP'];

$mail = new phpmailer();
$mail->PluginDir = dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/";
$mail->Mailer 	= "smtp";
$mail->SMTPAuth = true;
$mail->Host 	= "$host";
$mail->Username = "$Username";
$mail->Password = "$Password";
$mail->Port = "$Port";
$mail->SMTPSecure= 'ssl'; 
$mail->From 	= "sergio.pechoante@biggi.cl";		
$mail->FromName = "Sergio Pechoante";
$mail->Timeout=30;
$mail->Subject = $asunto;

$mail->ClearAddresses();

$mail->AddAddress('ascianca@biggi.cl', 'Angel Scianca');
$mail->AddAddress('mscianca@todoinox.cl', 'Margarita Scianca');
$mail->AddAddress('sergio.pechoante@biggi.cl', 'Sergio Pechoante');
//$mail->AddAddress('rescudero@biggi.cl', 'Rafael Escudero');

$mail->AddBCC('mherrera@biggi.cl', 'Marcelo Herrera');	
//$mail->AddBCC('ecastillo@biggi.cl', 'Eduardo Castillo');
$mail->AddBCC('vmelo@integrasystem.cl', 'Victor Melo');			

$mail->Body = $html;
$mail->AltBody = "";
$mail->ContentType="text/html";
$exito = $mail->Send();

if(!$exito){
	echo "Problema al enviar correo electrónico";
}

echo "Enviado";

?>