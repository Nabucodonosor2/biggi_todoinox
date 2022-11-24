function dlg_agrega_comision(){
	var args = "location:no;dialogLeft:400px;dialogTop:200px;dialogWidth:380px;dialogHeight:200px;dialogLocation:0;Toolbar:no;";
	returnVal = window.showModalDialog("dlg_agrega_comision.php", "_blank", args);
	if (returnVal == null)
 		return false;
	else{		
		document.getElementById('wo_hidden').value = returnVal;
   		return true;
	}
}

function calculo_porc_comision(){
	var vl_porc_comision_otros	= get_value('PORC_COMISION_OTROS_0').replace(",",".", 'g');
	var vl_total_otros			= get_value('TOTAL_OTROS_S_0').replace(".","", 'g');
	var vl_total_comision		= get_value('TOTAL_COMISION_0').replace(".","", 'g');
	var vl_new_total_comision	= (parseFloat(vl_total_otros) * (parseFloat(vl_porc_comision_otros))/100);

	var aTR = get_TR('ITEM_PAGO_COMISION');
	for(i=0 ; i < aTR.length ; i++){
		var vl_record = get_num_rec_field(aTR[i].id);
		var vl_porc_comision = get_value('PORC_COMISION_'+vl_record).replace(",",".");
		
		var vl_new_monto_comision = parseFloat(vl_new_total_comision) * (parseFloat(vl_porc_comision)/100);
		set_value('MONTO_COMISION_H_'+vl_record, vl_new_monto_comision, vl_new_monto_comision);
		vl_new_monto_comision = number_format(vl_new_monto_comision, 0, ',', '.');
		set_value('MONTO_COMISION_'+vl_record, vl_new_monto_comision, vl_new_monto_comision);
	}
	
	vl_new_total_comision = number_format(vl_new_total_comision, 0, ',', '.');
	set_value('TOTAL_COMISION_0', vl_new_total_comision, vl_new_total_comision);
}