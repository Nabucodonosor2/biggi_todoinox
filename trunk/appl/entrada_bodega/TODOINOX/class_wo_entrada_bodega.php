<?php
//require_once(dirname(__FILE__) . "/../../../../../commonlib/trunk/php/auto_load.php");
class wo_entrada_bodega extends wo_entrada_bodega_base {
	function wo_entrada_bodega() {      
		parent::wo_entrada_bodega_base(); 
	}
	function crear_entrada_desde($valor_devuelto) {
		
		list($opcion, $nro_opcion)=split('[|]', $valor_devuelto);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if ($opcion=='desde_ri'){

			
				$sql = "select NUMERO_REGISTRO_INGRESO 				
						from REGISTRO_INGRESO_4D 
						where NUMERO_REGISTRO_INGRESO = $nro_opcion";
			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('El Nº Registro Ingreso '.$nro_opcion.' no existe');								
				return;
			}
			
			session::set('IB_CREAR_DESDE_RI', $nro_opcion);
			$this->add();
			
		}else if($opcion=='desde_oc'){
			$sql = "SELECT COD_ORDEN_COMPRA
					FROM ORDEN_COMPRA
					WHERE COD_ORDEN_COMPRA = $nro_opcion";
					
			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$nro_opcion.' no existe');								
				return;
			}
			
			$sql = "SELECT COD_ESTADO_ORDEN_COMPRA
					FROM ORDEN_COMPRA
					WHERE COD_ORDEN_COMPRA = $nro_opcion
					AND COD_ESTADO_ORDEN_COMPRA <> 2";		
			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$nro_opcion.' está Anulada');								
				return;
			}
			
			$sql = "SELECT COD_ESTADO_ORDEN_COMPRA
					FROM ORDEN_COMPRA
					WHERE COD_ORDEN_COMPRA = $nro_opcion
					AND ES_INVENTARIO = 'S'";		
			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$nro_opcion.' no es Inventario');								
				return;
			}
			
			$sql = "SELECT COD_DOC, TIPO_DOC 
					FROM ENTRADA_BODEGA
					WHERE COD_DOC = $nro_opcion
					AND TIPO_DOC = 'ORDEN_COMPRA'";		
			$result = $db->build_results($sql);
			if (count($result) > 0){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$nro_opcion.' ya existe en Bodega');								
				return;
			}
			
			session::set('IB_CREAR_DESDE_OC', $nro_opcion);
			$this->add();
		}
	}
	function procesa_event() {		
		if(isset($_POST['b_create_x']))
			$this->crear_entrada_desde($_POST['wo_hidden']);
		else
			parent::procesa_event();
	}
}
?>