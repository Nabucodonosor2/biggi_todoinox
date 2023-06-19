<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
require_once("../../../appl.ini");

$sql = base64_decode($_REQUEST['token']);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);	
$result = $db->build_results($sql);

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
$pdf->Cell(520, 20, 'LISTADO PRODUCTOS SISTEMA WEB COMERCIAL TODOINOX' , 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->SetXY(475, 60);
$pdf->Cell(85, 15, 'FECHA: '.$result_fecha[0]['FECHA_ACTUAL'] , 0, 0, 'R');
$pdf->SetXY(475, 75);
$pdf->Cell(85, 15, 'HORA: '.$result_fecha[0]['HORA_ACTUAL'] , 0, 0, 'R');

//Header Table
$pdf->SetFont('Arial','B', 10);
$pdf->SetXY(30, 100);
$pdf->Cell(85, 15, 'Modelo' , 1, 0, 'C');
$pdf->SetXY(115, 100);
$pdf->Cell(215, 15, 'Descripción' , 1, 0, 'C');
$pdf->SetXY(330, 100);
$pdf->Cell(55, 15, 'Precio' , 1, 0, 'C');
$pdf->SetXY(385, 100);
$pdf->Cell(70, 15, 'Marca' , 1, 0, 'C');
$pdf->SetXY(455, 100);
$pdf->Cell(85, 15, 'Tipo Producto' , 1, 0, 'C');
$pdf->SetXY(540, 100);
$pdf->Cell(32, 15, 'Stock' , 1, 0, 'C');

//Body Table
$y_line = 115;
$pdf->SetFont('Arial','', 10);
$pdf->SetTextColor(0, 0, 0);

for ($i=0; $i < count($result); $i++){
    $nom_producto   = $result[$i]['NOM_PRODUCTO'];
    $precio         = number_format($result[0]['PRECIO_VENTA_PUBLICO'],0,',','.');

    if(strlen($nom_producto) > 32)
        $nom_producto = substr($nom_producto, 0, 32).'...';

    $pdf->SetXY(30, $y_line);
    $pdf->Cell(85, 15, $result[$i]['COD_PRODUCTO'], 1, 0, 'L');
    $pdf->SetXY(115, $y_line);
    $pdf->Cell(215, 15, $nom_producto, 1, 0, 'L');
    $pdf->SetXY(330, $y_line);
    $pdf->Cell(55, 15, $precio, 1, 0, 'R');
    $pdf->SetXY(385, $y_line);
    $pdf->Cell(70, 15, $result[$i]['NOM_MARCA'], 1, 0, 'L');
    $pdf->SetXY(455, $y_line);
    $pdf->Cell(85, 15, $result[$i]['NOM_TIPO_PRODUCTO'], 1, 0, 'L');
    $pdf->SetXY(540, $y_line);
    $pdf->Cell(32, 15, $result[$i]['STOCK'], 1, 0, 'R');

    $y_line = $pdf->getY() + 15;

    if($y_line == 520){
        $pdf->AddPage();
        $y_line = 40;
    }
}

$pdf->Output('Productos', 'I');
?>