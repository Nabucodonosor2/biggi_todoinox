<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_cot_extranjera extends w_output_biggi
{
	function wo_cx_cot_extranjera() {		
		$this->b_add_visible  = true;
		
		$sql="SELECT C.COD_CX_COT_EXTRANJERA			
						,C.FECHA_CX_COT_EXTRANJERA        
						,C.COD_USUARIO                    
						,U.NOM_USUARIO
						,C.CORRELATIVO_COT_EXTRANJERA     
						,C.COD_CX_ESTADO_COT_EXTRANJERA   
						,DBO.F_LAST_MOD('NOM_USUARIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) NOM_USUARIO_CAMBIO
					    ,DBO.F_LAST_MOD('FECHA_CAMBIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) FECHA_CAMBIO
						,P.ALIAS_PROVEEDOR_EXT
						,C.COD_PROVEEDOR_EXT
						,P.NOM_PROVEEDOR_EXT
						,CL.NOM_CX_CLAUSULA_COMPRA
						,CE.NOM_CX_ESTADO_COT_EXTRANJERA
						,P.DIRECCION
						,P.NOM_PAIS_4D NOM_PAIS
						,P.NOM_CIUDAD_4D NOM_CIUDAD
						,P.POST_OFFICE_BOX
						,C.COD_CX_CONTACTO_PROVEEDOR_EXT  
						,CC.TELEFONO
						,CC.MAIL
						,C.REFERENCIA                     
						,C.DELIVERY_DATE                  
						,C.COD_CX_PUERTO_SALIDA           
						,C.COD_CX_CLAUSULA_COMPRA         
						,C.COD_CX_PUERTO_ARRIBO           
						,C.COD_CX_MONEDA                  
						,C.PACKING                        
						,C.COD_CX_TERMINO_PAGO            
						,C.OBSERVACIONES                  
						,C.MONTO_TOTAL	                    
				FROM CX_COT_EXTRANJERA C
					,USUARIO U
					,PROVEEDOR_EXT P
					,CX_CONTACTO_PROVEEDOR_EXT CC
					,CX_CLAUSULA_COMPRA CL
					,CX_ESTADO_COT_EXTRANJERA CE
				WHERE CC.COD_CX_CONTACTO_PROVEEDOR_EXT= C.COD_CX_CONTACTO_PROVEEDOR_EXT
				  AND U.COD_USUARIO = C.COD_USUARIO
				  AND P.COD_PROVEEDOR_EXT = C.COD_PROVEEDOR_EXT
				  AND C.COD_CX_CLAUSULA_COMPRA = CL.COD_CX_CLAUSULA_COMPRA
				  AND C.COD_CX_ESTADO_COT_EXTRANJERA = CE.COD_CX_ESTADO_COT_EXTRANJERA
				ORDER BY C.COD_CX_COT_EXTRANJERA";
			
		parent::w_output_biggi('cx_cot_extranjera', $sql, $_REQUEST['cod_item_menu']);
		
		//formato numeros
		$this->dw->add_control(new static_num('RUT'));
		
		// headers
		$this->add_header(new header_num('COD_CX_COT_EXTRANJERA', 'COD_CX_COT_EXTRANJERA', 'Code'));
		$this->add_header(new header_text('ALIAS_PROVEEDOR_EXT', 'ALIAS_PROVEEDOR_EXT', 'Alias'));
		$this->add_header(new header_text('NOM_PROVEEDOR_EXT', 'NOM_PROVEEDOR_EXT', 'Provider Name'));
		$this->add_header(new header_text('NOM_CX_CLAUSULA_COMPRA', 'NOM_CX_CLAUSULA_COMPRA', 'Purchase'));     
		$this->add_header(new header_num('MONTO_TOTAL', 'MONTO_TOTAL', 'Total'));
		$this->add_header(new header_text('NOM_CX_ESTADO_COT_EXTRANJERA', 'NOM_CX_ESTADO_COT_EXTRANJERA', 'State'));
	}
}
?>