<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once("../../appl.ini");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);	
$sql_fecha = "SELECT FORMAT(GETDATE(), 'dd/MM/yy') FECHA_ACTUAL
                    ,FORMAT(GETDATE(), 'HH:mm:ss') HORA_ACTUAL";
$result_fecha = $db->build_results($sql_fecha);

$pdf = new FPDF('P','pt','letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

//Header
$pdf->SetFont('Arial','B',17);
$pdf->SetTextColor(0, 0, 128);
$pdf->SetXY(40, 40);
$pdf->Cell(520, 20, 'Hola Mundo' , 0, 0, 'L');

$pdf->Output('Revision_stock', 'I');
?>