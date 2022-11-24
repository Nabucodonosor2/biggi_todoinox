<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/class_PHPMailer.php");	
include(dirname(__FILE__)."/../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
ini_set("display_errors", "on");

//funcion que crea el objeto del phpmailer con sus parametros
function create_mail($asunto, $db){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$sql = "select dbo.f_get_parametro(53)         URL_SMTP
	                        ,dbo.f_get_parametro(54)      USER_SMTP
	                        ,dbo.f_get_parametro(55)      PASS_SMTP
	                        ,dbo.f_get_parametro(71)      PORT_SMTP";
	
	$result = $db->build_results($sql);
	$URL_SMTP   = $result[0]['URL_SMTP'];
	$USER_SMTP  = $result[0]['USER_SMTP'];
	$PASS_SMTP  = $result[0]['PASS_SMTP'];
	$PORT_SMTP  = $result[0]['PORT_SMTP'];

	$mail				= new phpmailer();
	$mail->PluginDir	= dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/";
	$mail->Mailer		= "smtp";
	$mail->SMTPAuth		= true;
	
	$mail->Host = $URL_SMTP;
	$mail->Username = $USER_SMTP;
	$mail->Password = $PASS_SMTP;
	$mail->Port = $PORT_SMTP;
	$mail->SMTPSecure= 'ssl';

    $mail->IsHTML(True);
	$mail->From			= "soporte@biggi.cl";		
	$mail->FromName		= "Comercial Biggi S.A.";
	$mail->Timeout		= 30;
	$mail->Subject		= $asunto;

	$mail->ClearAddresses();
	
	return $mail;
}

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$temp = new Template_appl('mail_producto_stock_c.htm');

//creacion de dw
$sql = "exec spdw_mail_producto_stock_c 0";
$dw = new datawindow($sql, "MAIL_PRODUCTO_MENOR");
$dw->retrieve();
$dw->habilitar($temp, false);

$sql = "exec spdw_mail_producto_stock_c 1";
$dw = new datawindow($sql, "MAIL_PRODUCTO_UNO");
$dw->retrieve();
$dw->habilitar($temp, false);

$sql = "exec spdw_mail_producto_stock_c 2";
$dw = new datawindow($sql, "MAIL_PRODUCTO_DOS");
$dw->retrieve();
$dw->habilitar($temp, false);

$sql = "exec spdw_mail_producto_stock_c 3";
$dw = new datawindow($sql, "MAIL_PRODUCTO_TRES");
$dw->retrieve();
$dw->habilitar($temp, false);

$sql = "exec spdw_mail_producto_stock_c 4";
$dw = new datawindow($sql, "MAIL_PRODUCTO_MAYOR");
$dw->retrieve();
$dw->habilitar($temp, false);

$sql = "SELECT CONVERT(VARCHAR, GETDATE(), 103) FECHA_ACTUAL";
$result = $db->build_results($sql);

$html = $temp->toString();
$asunto = 'Informe Stock Crítico Comercial Todoinox al '.$result[0]['FECHA_ACTUAL'];

$mail = create_mail($asunto, $db);

$mail->AddAddress('mscianca@todoinox.cl', 'MARGARITA SCIANCA');
$mail->AddAddress('lsun@todoinox.cl', 'LIFEN SUN');
$mail->AddBCC('mherrera@biggi.cl', 'MARCELO HERRERA');
//$mail->AddBCC('ecastillo@biggi.cl', 'EDUARDO CASTILLO');

$mail->Body = $html;
$mail->AltBody = "";
$mail->ContentType="text/html";
$mail->Send();

header('Location:mail_producto.htm');
?>