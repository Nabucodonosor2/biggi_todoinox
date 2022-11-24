function confirm_cambio(){
	var vl_cod_pago_faprov = get_value('COD_PAGO_FAPROV_COMERCIAL_0');
	var vl_confirm = confirm("Señor usuario se realizará el traspaso del PAGO FAPROV N° "+vl_cod_pago_faprov+" desde Comercial Biggi, y se\ningresará como INGRESO PAGO en Todoinox.");
	if(vl_confirm)
		return true;
	else
		return false;	
}