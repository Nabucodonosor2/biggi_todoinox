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
               FROM CX_OC_EXTRANJERA
               WHERE COD_CX_OC_EXTRANJERA >= 210
               ORDER BY COD_CX_OC_EXTRANJERA DESC";
			
      parent::w_output_biggi('cx_cuadro_embarque', $sql, $_REQUEST['cod_item_menu']);
      $this->dw->add_control(new static_num('MONTO_TOTAL'));
      
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
}
?>