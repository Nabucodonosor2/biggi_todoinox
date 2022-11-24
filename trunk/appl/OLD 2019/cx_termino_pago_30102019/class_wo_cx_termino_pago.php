<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_termino_pago extends w_output_biggi {
   function wo_cx_termino_pago() {   	
      $sql = "SELECT	COD_CX_TERMINO_PAGO
						,NOM_CX_TERMINO_PAGO 
			FROM CX_TERMINO_PAGO
			ORDER BY COD_CX_TERMINO_PAGO";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_termino_pago', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_TERMINO_PAGO', 'COD_CX_TERMINO_PAGO', 'Código'));
      $this->add_header(new header_text('NOM_CX_TERMINO_PAGO', 'NOM_CX_TERMINO_PAGO', 'Termino Pago'));
   }
}
?>