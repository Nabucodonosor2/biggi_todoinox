------------------  spu_pago_comision_tdnx  --------------------------
CREATE PROCEDURE dbo.spu_pago_comision_tdnx(@ve_operacion				 varchar(20)
										   ,@ve_cod_pago_comision_tdnx	 numeric
										   ,@ve_cod_usuario				 numeric=NULL
										   ,@ve_fecha_desde				 datetime=NULL
										   ,@ve_fecha_hasta				 datetime=NULL
										   ,@ve_cod_estado_pago_com_tdnx numeric=NULL
										   ,@ve_porc_comision_otros		 numeric(14,2)=NULL)
AS
BEGIN
	if (@ve_operacion='INSERT') begin
		INSERT INTO PAGO_COMISION_TDNX
					(FECHA_REGISTRO
					,COD_USUARIO
					,FECHA_DESDE
					,FECHA_HASTA
					,COD_ESTADO_PAGO_COMISION_TDNX
					,PORC_COMISION_OTROS)
			  VALUES(GETDATE()
			  		,@ve_cod_usuario
			  		,@ve_fecha_desde
			  		,@ve_fecha_hasta
			  		,@ve_cod_estado_pago_com_tdnx
			  		,@ve_porc_comision_otros)
	end 
	if (@ve_operacion='UPDATE') begin
		UPDATE PAGO_COMISION_TDNX
		SET COD_ESTADO_PAGO_COMISION_TDNX	= @ve_cod_estado_pago_com_tdnx
			,PORC_COMISION_OTROS			= @ve_porc_comision_otros
		WHERE COD_PAGO_COMISION_TDNX		= @ve_cod_pago_comision_tdnx
		
	end
	else if (@ve_operacion='DELETE') begin
		DELETE ITEM_US_PAGO_COMISION_TDNX
    	WHERE COD_PAGO_COMISION_TDNX = @ve_cod_pago_comision_tdnx
		
		DELETE PAGO_COMISION_TDNX
    	WHERE COD_PAGO_COMISION_TDNX = @ve_cod_pago_comision_tdnx
	end
END