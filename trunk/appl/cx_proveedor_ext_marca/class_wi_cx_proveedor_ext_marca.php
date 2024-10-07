<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

class dw_revision_stock extends datawindow {
    function dw_revision_stock() {
        $sql = "EXEC dbo.spdw_revision_stock {KEY1}";
        
        parent::datawindow($sql, 'REVISION_STOCK', true, true);
    }
}

class wi_cx_proveedor_ext_marca extends w_input{
	function wi_cx_proveedor_ext_marca($cod_item_menu){
		parent::w_input('cx_proveedor_ext_marca', $cod_item_menu);

		$sql = "SELECT CPE.COD_CX_PROVEEDOR_EXT_MARCA 
                    ,PE.COD_PROVEEDOR_EXT
                    ,ALIAS_PROVEEDOR_EXT
                    ,NOM_PROVEEDOR_EXT
                    ,M.NOM_MARCA
                FROM PROVEEDOR_EXT PE
                    ,CX_PROVEEDOR_EXT_MARCA CPE
                    ,MARCA M
                WHERE CPE.COD_CX_PROVEEDOR_EXT_MARCA = {KEY1}
                AND PE.COD_PROVEEDOR_EXT = CPE.COD_PROVEEDOR_EXT
                AND CPE.COD_MARCA = M.COD_MARCA";

		$this->dws['dw_cx_proveedor_ext_marca'] = new datawindow($sql);
        $this->dws['dw_revision_stock'] = new dw_revision_stock();
	}

	function load_record(){
		$cod_cx_proveedor_ext_marca = $this->get_item_wo($this->current_record, 'COD_CX_PROVEEDOR_EXT_MARCA');
		$this->dws['dw_cx_proveedor_ext_marca']->retrieve($cod_cx_proveedor_ext_marca);
        $cod_proveedor_ext = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_PROVEEDOR_EXT');
        $this->dws['dw_revision_stock']->retrieve($cod_proveedor_ext);
	}
    
	function get_key(){
		return $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_CX_PROVEEDOR_EXT_MARCA');
	}

    function habilita_boton(&$temp, $boton, $habilita){
		parent::habilita_boton($temp, $boton, $habilita);
		$ruta_imag = '../../../../commonlib/trunk/images/';
		if (defined('K_CLIENTE')) {
			if (file_exists('../../images_appl/'.K_CLIENTE.'/images/b_'.$boton.'.jpg')){
				$ruta_imag = '../../images_appl/'.K_CLIENTE.'/images/';
			}
		}

		if($boton == 'export'){
            if ($habilita){
                $control = '<input name="b_'.$boton.'" id="b_'.$boton.'" src="'.$ruta_imag.'b_'.$boton.'.jpg" type="image" '.
                                     'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_click.jpg\',1)" '.
                                     'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                                     'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\''.$ruta_imag.'b_'.$boton.'_over.jpg\',1)" ';
                $control .= '/>';
                $temp->setVar("WI_".strtoupper($boton), $control);
            }else{
                $temp->setVar("WI_".strtoupper($boton), '<img src="'.$ruta_imag.'b_'.$boton.'_d.jpg"/>');
            }
        }
	}

    function navegacion($temp){
        parent::navegacion($temp);
        $arr_date = explode('/', $this->current_date());
        $year = $arr_date[2];
        $cod_proveedor_ext = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_PROVEEDOR_EXT');

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql = "SELECT CORRELATIVO_OC
                FROM CX_OC_EXTRANJERA
                WHERE COD_PROVEEDOR_EXT = $cod_proveedor_ext
                AND ETA_DATE > GETDATE()";
        $result = $db->build_results($sql);

        $temp->setVar("H_NEXT_CORRELATIVO", $result[0]['CORRELATIVO_OC']);
        $temp->setVar("H_VENTA_TRES", 'VENTA '.($year - 3));
        $temp->setVar("H_VENTA_DOS", 'VENTA '.($year - 2));
        $temp->setVar("H_VENTA_UNO", 'VENTA '.($year - 1));
        $temp->setVar("H_VENTA_HOY", 'VENTA al '.$this->current_date());

        $this->habilita_boton($temp, 'export', true);
    }

    function procesa_event() {		
		if(isset($_POST['b_export_x']))
			$this->export_record();
        else if(isset($_POST['b_print_x']))
			$this->print_record();
		else
			parent::procesa_event();
	}

    function print_record(){
        print " <script>window.open('print_especial.php')</script>";
	    $this->_load_record();
    }

    function export_record(){
        error_reporting(E_ALL & ~E_NOTICE);
		require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php");
		require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php");
		
        $alias = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'ALIAS_PROVEEDOR_EXT');

		$fname = tempnam("/tmp", "resumen.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = $workbook->addworksheet('producto');
		
        $text =& $workbook->addformat();
		$text->set_font("Arial");
        $text->set_align('center');
        $text->set_size(12);

        $text_bold_blue =& $workbook->addformat();
		$text_bold_blue->copy($text);
		$text_bold_blue->set_bold(1);
        $text_bold_blue->set_color('blue');

        $text_item =& $workbook->addformat();
		$text_item->copy($text);
		$text_item->set_top(1);
		$text_item->set_bottom(1);
		$text_item->set_right(1);
		$text_item->set_left(1);
        $text_item->set_size(10);
        $text_item->set_valign('vcenter');
        $text_item->set_text_wrap();

		$worksheet->set_column(0, 8, 9);

        $worksheet->write(0, 0, 'REVISIÓN STOCK '.$alias, $text_bold_blue);
        $worksheet->merge_cells(0, 0, 0, 8);
        $worksheet->write(1, 0, 'AL '.$this->current_date(), $text_bold_blue);
        $worksheet->merge_cells(1, 0, 1, 8);

        $arr_date = explode('/', $this->current_date());
        $year = $arr_date[2];
        $cod_proveedor_ext = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_PROVEEDOR_EXT');

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql = "SELECT CORRELATIVO_OC
                FROM CX_OC_EXTRANJERA
                WHERE COD_PROVEEDOR_EXT = $cod_proveedor_ext
                AND ETA_DATE > GETDATE()";
        $result = $db->build_results($sql);

        $worksheet->set_row(2, 53);
        $worksheet->write(2, 0, 'Modelo', $text_item);
        $worksheet->write(2, 1, 'Stock', $text_item);
        $worksheet->write(2, 2, $result[0]['CORRELATIVO_OC'], $text_item);
        $worksheet->write(2, 3, 'STOCK NOMINAL', $text_item);
        $worksheet->write(2, 4, 'VENTA '.($year - 3), $text_item);
        $worksheet->write(2, 5, 'VENTA '.($year - 2), $text_item);
        $worksheet->write(2, 6, 'VENTA '.($year - 1), $text_item);
        $worksheet->write(2, 7, 'VENTA al '.$this->current_date(), $text_item);
        $worksheet->write(2, 8, 'Cant a pedir ALAN 3/2024', $text_item);

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "EXEC dbo.spdw_revision_stock $cod_proveedor_ext";	
		$result = $db->build_results($sql);

        $ln = 0;
        for ($i=0 ; $i < count($result); $i++){
			$COD_PRODUCTO       = $result[$i]['COD_PRODUCTO'];
			$STOCK              = $result[$i]['STOCK'];
			$NEXT_CORRELATIVO   = $result[$i]['NEXT_CORRELATIVO'];
			$STOCK_NOMINAL      = $result[$i]['STOCK_NOMINAL'];
			$VENTAS_TRES        = $result[$i]['VENTAS_TRES'];
			$VENTAS_DOS         = $result[$i]['VENTAS_DOS'];
            $VENTAS_UNO         = $result[$i]['VENTAS_UNO'];
            $VENTAS_HOY         = $result[$i]['VENTAS_HOY'];
			
            $worksheet->write(3+$ln, 0, $COD_PRODUCTO, $text_item);
            $worksheet->write(3+$ln, 1, $STOCK, $text_item);
            $worksheet->write(3+$ln, 2, $NEXT_CORRELATIVO, $text_item);
            $worksheet->write(3+$ln, 3, $STOCK_NOMINAL, $text_item);
            $worksheet->write(3+$ln, 4, $VENTAS_TRES, $text_item);
            $worksheet->write(3+$ln, 5, $VENTAS_DOS, $text_item);
            $worksheet->write(3+$ln, 6, $VENTAS_UNO, $text_item);
            $worksheet->write(3+$ln, 7, $VENTAS_HOY, $text_item);
            $worksheet->write(3+$ln, 8, '', $text_item);
            $ln++;
		}

		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"$alias - REVISIÓN prueba.xls\"");
		header("Content-Disposition: inline; filename=\"$alias - REVISIÓN prueba.xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		
		error_reporting(E_ALL);
    }
}
?>