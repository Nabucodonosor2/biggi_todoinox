<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_puerto_salida extends w_output_biggi {
   function wo_cx_puerto_salida() {   	
      $sql = "SELECT COD_CX_PUERTO_SALIDA
					,NOM_CX_PUERTO_SALIDA 
			FROM CX_PUERTO_SALIDA
			ORDER BY COD_CX_PUERTO_SALIDA";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_puerto_salida', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_PUERTO_SALIDA', 'COD_CX_PUERTO_SALIDA', 'Código'));
      $this->add_header(new header_text('NOM_CX_PUERTO_SALIDA', 'NOM_CX_PUERTO_SALIDA', 'Puerto Salida'));
   }
}
?>