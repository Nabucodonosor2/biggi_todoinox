<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_oc_extranjera extends w_output_biggi{
	function wo_cx_oc_extranjera(){		
		$this->b_add_visible  = true;
		
		$sql="SELECT  C.COD_CX_OC_EXTRANJERA
						,C.CORRELATIVO_OC 			
						,CONVERT(VARCHAR, C.FECHA_CX_OC_EXTRANJERA, 103) FECHA_CX_OC_EXTRANJERA
						,FECHA_CX_OC_EXTRANJERA DATE_FECHA_CX_OC_EXTRANJERA
						,P.ALIAS_PROVEEDOR_EXT
						,P.NOM_PROVEEDOR_EXT
						,C.MONTO_TOTAL			                  	                
			 FROM  CX_OC_EXTRANJERA C, PROVEEDOR_EXT P
			 WHERE C.COD_PROVEEDOR_EXT = P.COD_PROVEEDOR_EXT
			 ORDER BY C.COD_CX_OC_EXTRANJERA DESC";
			
		parent::w_output_biggi('cx_oc_extranjera', $sql, $_REQUEST['cod_item_menu']);
		
		// headers
		$this->add_header(new header_num('COD_CX_OC_EXTRANJERA', 'COD_CX_OC_EXTRANJERA', 'Code'));
		$this->add_header(new header_text('CORRELATIVO_OC', 'CORRELATIVO_OC', 'Correlative'));
		$this->add_header($control = new header_date('FECHA_CX_OC_EXTRANJERA', 'CONHVERT(VARCHAR, C.FECHA_CX_OC_EXTRANJERA, 103)', 'Date'));
		$control->field_bd_order = 'DATE_FECHA_CX_OC_EXTRANJERA';
		$this->add_header(new header_text('ALIAS_PROVEEDOR_EXT', 'ALIAS_PROVEEDOR_EXT', 'Alias'));
		$this->add_header(new header_text('NOM_PROVEEDOR_EXT', 'NOM_PROVEEDOR_EXT', 'Provider Name'));
		$this->add_header(new header_num('MONTO_TOTAL', 'MONTO_TOTAL', 'Total'));
	}
	
	function crear_desde_oc($opcion){
		$opcion = explode("|", $opcion);
		if($opcion[0] == 'CREAR_DESDE'){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql= "SELECT COD_CX_OC_EXTRANJERA 
				   FROM CX_OC_EXTRANJERA
				   WHERE COD_CX_OC_EXTRANJERA = $opcion[1]";
			$result = $db->build_results($sql);
			if(count($result) == 0){
				$this->_redraw();
				$this->alert("La Orden de Compra N° ".$opcion[1]." no existe");
			}else{
				session::set('COD_CX_OC_EXTRANJERA_CD', $opcion[1]);
				$this->add();
			}
		}else if($opcion[0] == 'DUPLICAR'){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$db->BEGIN_TRANSACTION();	
			$sp = 'spu_cx_oc_extranjera';
			$param = "'DUPLICAR'
					 ,$opcion[1]
					 ,null
					 ,null
					 ,$opcion[2]";
					 
			if($db->EXECUTE_SP($sp, $param)){ 
				$db->COMMIT_TRANSACTION();
				$this->retrieve();
			}else{ 
				$db->ROLLBACK_TRANSACTION();
				$this->_redraw();
				$this->alert("No se pudo crear la factura. Error en 'spu_cx_oc_extranjera', favor contacte a IntegraSystem.");
			}
		}else if($opcion[0] == 'CREAR_DESDE_QUOTE'){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql= "SELECT COD_CX_COT_EXTRANJERA
				   FROM CX_COT_EXTRANJERA
				   WHERE COD_CX_COT_EXTRANJERA = ".$opcion[1];
			$result = $db->build_results($sql);
			if(count($result) == 0){
				$this->_redraw();
				$this->alert("La Corización N° ".$opcion[1]." no existe");
			}else{
				session::set('COD_CX_COT_EXTRANJERA_CD', $opcion[1]);
				$this->add();
			}
		}else{
			$this->_redraw();
		}
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='create'){
			$ruta_over = "'../../../../commonlib/trunk/images/b_create_over.jpg'";
			$ruta_out = "'../../../../commonlib/trunk/images/b_create.jpg'";
			$ruta_click = "'../../../../commonlib/trunk/images/b_create_click.jpg'";
			$temp->setVar("WO_CREATE", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
							'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_'.$boton.'.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
							'onClick="dlg_crea_desde_oc();" />');
		}else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	function redraw(&$temp){
		$this->habilita_boton($temp, 'create', true);
	}
	function procesa_event(){
		if(isset($_POST['b_create_x']))
			$this->crear_desde_oc($_POST['wo_hidden']);
		else
			parent::procesa_event();
	}
}
?>