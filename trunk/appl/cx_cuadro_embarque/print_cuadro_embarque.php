<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once("../../appl.ini");

$sql = base64_decode($_REQUEST['token']);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);	
$result = $db->build_results($sql);

$pdf = new FPDF('P','pt','letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

//Header
$pdf->SetFont('Arial','B',17);
$pdf->SetTextColor(0, 0, 128);
$pdf->SetXY(40, 40);
$pdf->Cell(520, 20, 'CUADRO DE EMBARQUES PENDIENTES' , 1, 0, 'C');

//Header table
$pdf->SetFont('Arial','B', 10);
$pdf->SetXY(40, 80);
$pdf->Cell(140, 20, 'Pedido' , 1, 0, 'C');
$pdf->SetXY(180, 80);
$pdf->Cell(250, 20, 'Mercaderia' , 1, 0, 'C');

$pdf->Output('Cuadro de embarque', 'I');
?>