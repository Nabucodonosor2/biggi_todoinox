<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_observacion_tipo extends w_output_biggi {
   function wo_cx_observacion_tipo() {   	
      $sql ="SELECT	COD_CX_OBSERVACION_TIPO
					,NOM_CX_OBSERVACION_TIPO
					,TEXTO
			FROM CX_OBSERVACION_TIPO
			ORDER BY COD_CX_OBSERVACION_TIPO";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_observacion_tipo', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_OBSERVACION_TIPO', 'COD_CX_OBSERVACION_TIPO', 'Código'));
      $this->add_header(new header_text('NOM_CX_OBSERVACION_TIPO', 'NOM_CX_OBSERVACION_TIPO', 'Observación'));
      $this->add_header(new header_text('TEXTO', 'TEXTO', 'Texto'));
   }
}
?>