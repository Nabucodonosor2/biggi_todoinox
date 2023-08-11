<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_cuadro_embarque extends w_output_biggi{
   function wo_cx_cuadro_embarque(){

      $sql = "SELECT COD_CX_OC_EXTRANJERA
                     ,CORRELATIVO_OC
                     ,REFERENCIA
                     ,CONVERT(VARCHAR, DELIVERY_DATE, 103) DELIVERY_DATE
                     ,DELIVERY_DATE DATE_DELIVERY_DATE
                     ,CONVERT(VARCHAR, FECHA_ZARPE, 103) FECHA_ZARPE
                     ,FECHA_ZARPE DATE_FECHA_ZARPE
                     ,CONVERT(VARCHAR, ETA_DATE, 103) ETA_DATE
                     ,ETA_DATE DATE_ETA_DATE
                     ,MONTO_TOTAL
                     ,SUBSTRING(CORRELATIVO_OC, CHARINDEX('/', CORRELATIVO_OC)+1, 4) +
                     SUBSTRING(REPLACE(CORRELATIVO_OC, ' ', ''), 0, LEN(REPLACE(CORRELATIVO_OC, ' ', ''))-5) +
                     SUBSTRING(CORRELATIVO_OC, CHARINDEX('/', CORRELATIVO_OC)-1, 1) FIELD_ORDER
                     ,CASE	
                        WHEN COD_CX_MONEDA = 1 THEN 'USD'
                        WHEN COD_CX_MONEDA = 2 THEN 'EUR'
                     END CURRENCY
                     ,CONVERT(VARCHAR, GETDATE(), 103) ACTUAL_DATE
               FROM CX_OC_EXTRANJERA
               WHERE INCLUIR_CUADRO_EMBARQUE = 'S'
               ORDER BY FIELD_ORDER ASC";
			
      parent::w_output_biggi('cx_cuadro_embarque', $sql, $_REQUEST['cod_item_menu']);
      $this->dw->add_control(new static_num('MONTO_TOTAL', 2));
      
      // headers
      $this->add_header(new header_num('COD_CX_OC_EXTRANJERA', 'COD_CX_OC_EXTRANJERA', 'Nro PO'));
      $this->add_header(new header_text('CORRELATIVO_OC', 'CORRELATIVO_OC', 'Pedido'));
      $this->add_header(new header_text('REFERENCIA', 'REFERENCIA', 'Referencia'));
      $this->add_header($control = new header_date('DELIVERY_DATE', 'DELIVERY_DATE', 'Entrega D/D'));
      $control->field_bd_order = 'DATE_DELIVERY_DATE';
      $this->add_header($control = new header_date('FECHA_ZARPE', 'FECHA_ZARPE', 'Zarpe ETD'));
      $control->field_bd_order = 'DATE_FECHA_ZARPE';
      $this->add_header($control = new header_date('ETA_DATE', 'ETA_DATE', 'Llegada ETA'));
      $control->field_bd_order = 'DATE_ETA_DATE';
      $this->add_header(new header_num('MONTO_TOTAL', 'MONTO_TOTAL', 'Invoice'));
   }

   function habilita_boton($temp, $boton, $habilita){
      parent::habilita_boton($temp, $boton, $habilita);

		if($boton=='print'){
			$ruta_over = "'../../../../commonlib/trunk/images/b_print_over.jpg'";
			$ruta_out = "'../../../../commonlib/trunk/images/b_print.jpg'";
			$ruta_click = "'../../../../commonlib/trunk/images/b_print_click.jpg'";
			$temp->setVar("WO_PRINT", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
							'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_'.$boton.'.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
							'onClick="print_ce();" />');
		}	
	}

	function redraw(&$temp){
      parent::redraw($temp);
		$this->habilita_boton($temp, 'print', true);
	}

   function procesa_event(){
		if(isset($_POST['b_print_x']))
			$this->print_cuadro_embarque(); //$_POST['wo_hidden'])
		else
			parent::procesa_event();
	}

   function redraw_item(&$temp, $ind, $record){
		parent::redraw_item($temp, $ind, $record);
      $arr_fecha_actual  = explode('/', $this->current_date());
      $arr_fecha_eta     = explode('/', $this->dw->get_item($record, 'ETA_DATE'));

      if($arr_fecha_actual[1] == $arr_fecha_eta[1] && $arr_fecha_actual[2] == $arr_fecha_eta[2])
         $temp->setVar("wo_registro.CSS_COLOR_MES", "#ffc");
      else
         $temp->setVar("wo_registro.CSS_COLOR_MES", "");
		
	}

   function print_cuadro_embarque(){
		$sql = base64_encode($this->dw->get_sql());
		print " <script>window.open('../cx_cuadro_embarque/print_cuadro_embarque.php?token=$sql','')</script>";
		$this->_redraw();
   }
}
?>