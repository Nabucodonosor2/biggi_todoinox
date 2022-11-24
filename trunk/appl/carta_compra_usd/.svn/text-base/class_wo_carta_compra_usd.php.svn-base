<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_header_modelo.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_parametrica_biggi.php");

class wo_carta_compra_usd extends w_parametrica_biggi
{
   function wo_carta_compra_usd()
   {   	
      $sql = "SELECT CC.COD_CX_CARTA_COMPRA_USD
					,EC.NOM_ESTADO_CARTA_COMPRA
					,CC.ATENCION
					,CC.TIPO_CAMBIO_USD
					,CC.CANT_COMPRA_USD
					,CC.TOTAL_DEBITO_PESOS
					,CC.REFERENCIA
					,CONVERT(VARCHAR,CC.FECHA_CX_CARTA_COMPRA_USD,103) FECHA_CX_CARTA_COMPRA_USD
					,CC.FECHA_REGISTRO
					FROM CX_CARTA_COMPRA_USD CC, ESTADO_CARTA_USD EC
					WHERE CC.COD_ESTADO_CARTA_COMPRA = EC.COD_ESTADO_CARTA_COMPRA
					ORDER BY FECHA_REGISTRO DESC";
			
      parent::w_parametrica_biggi('carta_compra_usd', $sql, $_REQUEST['cod_item_menu'], '3516');
      //////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////////////////////
      //// AGREGAAAAAAAAAAAAAAAAAAAARRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
      ////////// CONTROLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSS
      ///////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////////////////////
      
      $this->add_header(new header_num('COD_CX_CARTA_COMPRA_USD', 'COD_CX_CARTA_COMPRA_USD', 'Código'));
      $this->add_header(new header_text('NOM_ESTADO_CARTA_COMPRA', 'NOM_ESTADO_CARTA_COMPRA', 'Estado'));
      $this->add_header(new header_num('ATENCION', 'ATENCION', 'Atencion'));
      $this->add_header(new header_text('TIPO_CAMBIO_USD', 'TIPO_CAMBIO_USD', 'Valor dolar CLP'));
      $this->add_header(new header_num('CANT_COMPRA_USD', 'CANT_COMPRA_USD', 'Compra USD'));
      $this->add_header(new header_num('TOTAL_DEBITO_PESOS', 'TOTAL_DEBITO_PESOS', 'Total CLP'));
      $this->add_header(new header_date('FECHA_CX_CARTA_COMPRA_USD', 'FECHA_CX_CARTA_COMPRA_USD', 'Fecha'));
   }
}
?>