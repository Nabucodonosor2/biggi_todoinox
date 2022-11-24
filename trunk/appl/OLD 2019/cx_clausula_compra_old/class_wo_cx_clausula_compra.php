<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_clausula_compra extends w_output_biggi {
   function wo_cx_clausula_compra() {   	
      $sql = "SELECT COD_CX_CLAUSULA_COMPRA
      				,NOM_CX_CLAUSULA_COMPRA 
			FROM 	CX_CLAUSULA_COMPRA
			ORDER 	BY COD_CX_CLAUSULA_COMPRA";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_CLAUSULA_COMPRA', 'COD_CX_CLAUSULA_COMPRA', 'C�digo'));
      $this->add_header(new header_text('NOM_CX_CLAUSULA_COMPRA', 'NOM_CX_CLAUSULA_COMPRA', 'Clausula Compra'));
   }
}
?>