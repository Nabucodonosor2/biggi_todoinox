function validate(){
	var vl_cod_estado_orden_despacho = get_value('COD_ESTADO_ORDEN_DESPACHO_0');

	if(vl_cod_estado_orden_despacho == 3){
		var vl_aTR = get_TR('ITEM_ORDEN_DESPACHO');	
		for(i=0 ; i < vl_aTR.length ; i++){
			var vl_record = get_num_rec_field(vl_aTR[i].id);
			
			if(!document.getElementById('CANTIDAD_RECIBIDA_'+vl_record))
				continue;
			
			var vl_cantidad = get_value('CANTIDAD_'+vl_record);
			var vl_cantidad_recibida = get_value('CANTIDAD_RECIBIDA_'+vl_record);
			
			if(vl_cantidad_recibida == '')
				vl_cantidad_recibida = 0;
			
			if(parseInt(vl_cantidad) != parseInt(vl_cantidad_recibida)){
				alert('Para guardar en estado ENTREGADO, la cantidad recibida debe ser igual a la cantidad emitida.');
				document.getElementById('CANTIDAD_RECIBIDA_'+vl_record).focus();
				return false;
			}
		}
	}else if(vl_cod_estado_orden_despacho == 4){
		var vl_motivo_anula = get_value('MOTIVO_ANULA_0');
		
		if(vl_motivo_anula == ''){
			alert('Debe ingresar un motivo.');
			document.getElementById('MOTIVO_ANULA_0').focus();
			return false;
		}
	}
	
	return true;
}

function display_anula(){
	var vl_cod_estado_orden_despacho = get_value('COD_ESTADO_ORDEN_DESPACHO_0');
	
	if(vl_cod_estado_orden_despacho == 4)
		document.getElementById('TR_ANULA').style.display = '';
	else
		document.getElementById('TR_ANULA').style.display = 'none';
}