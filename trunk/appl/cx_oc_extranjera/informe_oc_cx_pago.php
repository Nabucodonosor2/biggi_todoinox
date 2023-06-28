<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../envio_softland/FPDF/fpdf.php");

$sql = base64_decode($_REQUEST['sql']);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

$pdf = new FPDF('P','pt','letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,0);
$titulo = "Carta Orden Pago.pdf";
$pdf->SetTitle($titulo);

$y=-15;
$pdf->Image(dirname(__FILE__)."/../../images_appl/TODOINOX/logo_reporte_2.jpg", 20, 8, 460, 620);
$pdf->SetTextColor(0,0,10);//TEXTOS azul
$pdf->SetFont('Arial','',12);
$pdf->SetXY(380, $y+25+15);
$pdf->Cell(152, 15, 'Santiago, '.$result[0]['FECHA_CX_CARTA_OP'], 0, 0, 'L');
$pdf->SetTextColor(0,0,10);//TEXTOS azul
$pdf->SetFont('Arial','B',17);
$pdf->SetXY(210, $y+65+15);
$pdf->Cell(212, 17, 'CARTA ORDEN DE PAGO', 0, 0, 'L');

$pdf->SetTextColor(0,0,10);//TEXTOS azul
$pdf->SetFont('Arial','',12);
$pdf->SetXY(52, $y+86+15);
$pdf->Cell(70, 15, 'Señores', 0, 0, 'L');
$pdf->SetXY(52, $y+100+15);
$pdf->Cell(70, 15, 'ITAU', 0, 0, 'L');
$pdf->SetXY(52, $y+114+15);
$pdf->Cell(70, 15, 'PRESENTE', 0, 0, 'L');
$pdf->Line(55,128,110,128);

$pdf->SetXY(52, $y+155+10);
$pdf->Cell(158, 15, 'At.: Srita. Yasmina Guajardo ', 0, 0, 'L');

$pdf->SetXY(52, $y+219);
$pdf->MultiCell(450, 14, 'Por la presente autorizo debitar de cta. cte. US$ N°1200-2122-57, COMERCIAL TODOINOX LTDA., (Rut: 89.257.000-0) la suma de US$'.$result[0]['MONTO_PAGO'].' y enviar transferencia por pago anticipado de importación a:', 0, 'J', false);

$pdf->SetXY(52, $y+269);
$pdf->Cell(150, 15, 'NOMBRE BENEFICIARIO:', 0, 0, 'L');

$pdf->SetXY(85, $y+289);
$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_NAMEEMP'], 0, 'J', false);	
$pdf->SetXY(85, $y+304);
$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_DIREMP'], 0, 'J', false);

$pdf->SetXY(52, $y+360);
$pdf->Cell(140, 15, 'BANCO BENEFICIARIO:', 0, 0, 'L');

$pdf->SetXY(85, $y+380);
$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_NAMEBANK'], 0, 'J', false);
$pdf->SetXY(85, $y+395);
$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_DIRBANK'], 0, 'J', false);

$pdf->SetXY(85, $y+450);
$pdf->Cell(100, 15, 'ACCOUNT N°', 0, 0, 'L');
$pdf->SetXY(185, $y+450);
$pdf->Cell(158, 15, $result[0]['BP_ACCOUNT_NUMBER'], 0, 0, 'L');
$pdf->SetXY(85, $y+465);
$pdf->Cell(100, 15, 'SWIFT', 0, 0, 'L');
$pdf->SetXY(185, $y+465);
$pdf->Cell(158, 15, $result[0]['BP_SWIFT'], 0, 0, 'L');

$pdf->SetXY(52, $y+548);
$pdf->Cell(93, 15, 'GASTOS  OUR:', 0, 0, 'L');
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(145, $y+548);
$pdf->Cell(358, 15, 'VALUTA 24 HORAS. / REC FULL PAY', 0, 0, 'L');
$pdf->SetFont('Arial','',10);
$pdf->SetXY(52, $y+563);
$pdf->MultiCell(400, 14, 'Los gastos que genere esta operación cargarlos a nuestra cta. cte. nro. 0211-3432-51. Favor enviarnos copia de Swift por email.', 0, 'J', false);

$pdf->SetFont('Arial','',12);
$pdf->SetXY(52, $y+615);
$pdf->Cell(80, 15, 'Atentamente,', 0, 0, 'L');

$pdf->Line(55,690,230,690);
$pdf->SetXY(52, $y+710);
$pdf->Cell(180, 15, 'COMERCIAL TODOINOX LTDA.', 0, 0, 'L');

if($result[0]['COD_ESTADO_CX_CARTA_OP'] == 1)
    $pdf->Image(session::get('K_ROOT_DIR').'/images_appl/cx_po_emitida.png', 0, 400, 612, 250);

$pdf->Output("Carta Orden Pago.pdf", 'I');
?>