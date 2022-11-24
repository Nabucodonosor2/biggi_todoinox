<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_puerto_arribo extends w_output_biggi {
   function wo_cx_puerto_arribo() {   	
      $sql = "SELECT	COD_CX_PUERTO_ARRIBO
						,NOM_CX_PUERTO_ARRIBO 
			FROM 		CX_PUERTO_ARRIBO
			ORDER BY 	COD_CX_PUERTO_ARRIBO";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_puerto_arribo', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_PUERTO_ARRIBO', 'COD_CX_PUERTO_ARRIBO', 'C�digo'));
      $this->add_header(new header_text('NOM_CX_PUERTO_ARRIBO', 'NOM_CX_PUERTO_ARRIBO', 'Puerto Arribo'));
      
   }
}
?>