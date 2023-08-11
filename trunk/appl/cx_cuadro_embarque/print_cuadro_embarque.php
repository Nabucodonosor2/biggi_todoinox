<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once("../../appl.ini");

$sql = base64_decode($_REQUEST['token']);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);	
$result = $db->build_results($sql);

$pdf = new FPDF('L','pt','letter');
new_page($pdf); 
$posY_it   = $pdf->getY() + 20;

for ($j=0; $j < count($result); $j++){
    $posY_inicial = $posY_it;

    $fecha_zarpe    = ($result[$j]['FECHA_ZARPE']=='') ? "PDTE" : $result[$j]['FECHA_ZARPE'];
    $fecha_llegada  = ($result[$j]['ETA_DATE']=='') ? "PDTE" : $result[$j]['ETA_DATE'];

    $sql_item = "SELECT COD_PRODUCTO
                        ,CANTIDAD
                FROM CX_ITEM_OC_EXTRANJERA
                WHERE COD_CX_OC_EXTRANJERA = ".$result[$j]['COD_CX_OC_EXTRANJERA'];
    $result_item = $db->build_results($sql_item);

    if($posY_inicial > 470){
        $posY_it        = 100;
        $posY_inicial   = 100;
        new_page($pdf);
    }

    //Datos
    $pdf->SetFont('Arial','', 8);
    $pdf->SetTextColor(0, 0, 0);

    $count = 0;
    $count2 = 0;
    for ($i=0; $i < count($result_item); $i++){
        if($count == 0){
            $pdf->SetXY(180, $posY_it);
            $pdf->Cell(100, 20, $result_item[$i]['COD_PRODUCTO'], 1, 0, 'L');
            $pdf->SetXY(250, $posY_it);
            $pdf->Cell(30, 20, $result_item[$i]['CANTIDAD'], 0, 0, 'R');
            $count++;
            $count2 += 1;
        }else if($count == 1){
            $pdf->SetXY(280, $posY_it);
            $pdf->Cell(100, 20, $result_item[$i]['COD_PRODUCTO'], 1, 0, 'L');
            $pdf->SetXY(350, $posY_it);
            $pdf->Cell(30, 20, $result_item[$i]['CANTIDAD'], 0, 0, 'R');
            $count++;
        }else if($count == 2){
            $pdf->SetXY(380, $posY_it);
            $pdf->Cell(100, 20, $result_item[$i]['COD_PRODUCTO'], 1, 0, 'L');
            $pdf->SetXY(450, $posY_it);
            $pdf->Cell(30, 20, $result_item[$i]['CANTIDAD'], 0, 0, 'R');
            $count = 0;

            if($i+1 <> count($result_item))
                $posY_it += 20;
        }

        if($i+1 == count($result_item))
            $posY_it += 20;
        
    } 

    $sql_container =   "SELECT CONVERT(VARCHAR, CANT) + 'x' + NOM_CONTAINER CONTAINER
                        FROM CX_PACKING_OC_EXTRANJERA
                        WHERE COD_CX_OC_EXTRANJERA = ".$result[$j]['COD_CX_OC_EXTRANJERA'];
    $result_container = $db->build_results($sql_container);
    
    $pdf->SetXY(40, $posY_inicial);
    $pdf->Cell(90, $count2*20, $result[$j]['CORRELATIVO_OC'], 1, 0, 'C');
    $pdf->SetXY(130, $posY_inicial);
    $pdf->Multicell(50, $count2*20, $result_container[0]['CONTAINER'], 1, 'C');

    $pdf->SetXY(480, $posY_inicial);
    $pdf->Cell(26, $count2*20,  $result[$j]['CURRENCY'], 1, 0, 'C');
    $pdf->SetXY(506, $posY_inicial);
    $pdf->Cell(61, $count2*20, number_format($result[$j]['MONTO_TOTAL'], 2, ',', '.'), 1, 0, 'C');
    $pdf->SetXY(567, $posY_inicial);
    $pdf->Cell(61, $count2*20, $result[$j]['DELIVERY_DATE'], 1, 0, 'C');
    $pdf->SetXY(628, $posY_inicial);
    $pdf->Cell(61, $count2*20, $fecha_zarpe, 1, 0, 'C');
    $pdf->SetXY(689, $posY_inicial);
    $pdf->Cell(61, $count2*20, $fecha_llegada, 1, 0, 'C');

    $pdf->Line(40, $posY_it, 689, $posY_it);//linea rellena para que no deje espacios en blanco
}

$pdf->Output('Cuadro de embarque', 'I');

function new_page($pdf){
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);

    //Header
    $pdf->SetFont('Arial','B',17);
    $pdf->SetTextColor(0, 0, 128);
    $pdf->SetXY(40, 40);
    $pdf->Cell(710, 20, 'CUADRO DE EMBARQUES PENDIENTES' , 0, 0, 'C');

    //Header table
    $pdf->SetFont('Arial','B', 10);
    $pdf->SetXY(40, 80);
    $pdf->Cell(140, 20, 'PEDIDO' , 1, 0, 'C');
    $pdf->SetXY(180, 80);
    $pdf->Cell(300, 20, 'MERCADERIA' , 1, 0, 'C');
    $pdf->SetXY(480, 80);
    $pdf->Cell(26, 20, '$' , 1, 0, 'C');
    $pdf->SetXY(506, 80);
    $pdf->Cell(61, 20, 'Invoice' , 1, 0, 'C');
    $pdf->SetXY(567, 80);
    $pdf->Cell(61, 20, 'Entrega' , 1, 0, 'C');
    $pdf->SetXY(628, 80);
    $pdf->Cell(61, 20, 'ZARPE' , 1, 0, 'C');
    $pdf->SetXY(689, 80);
    $pdf->Cell(61, 20, 'LLEGADA' , 1, 0, 'C');
}
?>