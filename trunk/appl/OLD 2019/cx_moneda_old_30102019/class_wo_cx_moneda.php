<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_moneda extends w_output_biggi {
   function wo_cx_moneda() {   	
      $sql = "SELECT	COD_CX_MONEDA
						,NOM_CX_MONEDA
						,NUMERO_DECIMALES 
				FROM 	CX_MONEDA
				ORDER 	BY COD_CX_MONEDA";
			
    //  parent::w_parametrica('cx_clausula_compra', $sql, $_REQUEST['cod_item_menu'], '352005');
      parent::w_output_biggi('cx_moneda', $sql, $_REQUEST['cod_item_menu']);
      // headers
      $this->add_header(new header_text('COD_CX_MONEDA', 'COD_CX_MONEDA', 'Código'));
      $this->add_header(new header_text('NOM_CX_MONEDA', 'NOM_CX_MONEDA', 'Moneda'));
      $this->add_header(new header_text('NUMERO_DECIMALES', 'NUMERO_DECIMALES', 'Decimales'));
   }
}
?>