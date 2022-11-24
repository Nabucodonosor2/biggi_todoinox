
function valida_stock(cantidad){

	var cant = ""+cantidad.id;
    var i = cant.substring(9, 13);
    var num = cantidad.value.replace(",",".");
    var num = parseFloat(cantidad.value);
    
	var vl_cod_producto = document.getElementById('COD_PRODUCTO_'+i).value;
	
	var ajax = nuevoAjax();
		ajax.open("GET", "TODOINOX/ajax_salida_bodega.php?cod_producto="+vl_cod_producto, false);
		ajax.send(null);
		var resp_desde_nv = URLDecode(ajax.responseText);
		
		var resp = ajax.responseText.split('|');
		
		var maneja_inventario = resp[0];
		var stock = resp[1];
		
		if(resp[0] == 'N'){
		  alert('Este producto no maneja inventario, debe habilitarse previamente si desea modificar su stock');
		}else{
		  
		}
		
		if((stock-num) < 0){
		 alert('Una salida por '+num+' unidades, dejara el stock de este producto en negativo');
		}else{
		 
		}
		

}

function show_msg_ajuste(ve_campo){
	var record = get_num_rec_field(ve_campo.id);
	var vl_cod_producto = document.getElementById('COD_PRODUCTO_'+record).value;
	
	var ajax = nuevoAjax();
	ajax.open("GET", "TODOINOX/ajax_tiene_ajuste_salida.php?cod_producto="+vl_cod_producto, false);
	ajax.send(null);
	var resp = ajax.responseText;
	resp = resp.split('|');

	if(resp[0] == 'TIENE_AJUSTE')
		alert('ATENCION: El producto '+vl_cod_producto+' ya ha sido ajustado con anterioridad.\n\nFue ajustado con la '+resp[3]+' '+resp[4]+', el '+resp[1]+' por '+resp[2]+'.\n\nSe aconseja realizar revisión detallada antes de ajustar nuevamente.');
}