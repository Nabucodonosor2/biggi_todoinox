-------------------- spu_item_pago_comision_tdnx ---------------------------------
CREATE PROCEDURE spu_item_pago_comision_tdnx(@ve_operacion							varchar(20)
											,@ve_cod_item_us_pago_comision_tdnx		numeric
											,@ve_cod_usuario_pago_comision			numeric=NULL
											,@ve_porc_comision						numeric(14,2)=NULL
											,@ve_monto_comision						numeric=NULL
											,@ve_cod_pago_comision_tdnx				numeric=NULL)
AS
BEGIN
	if (@ve_operacion='INSERT') 
		begin
			INSERT INTO ITEM_US_PAGO_COMISION_TDNX(
				COD_USUARIO_PAGO_COMISION,
				PORC_COMISION,
				MONTO_COMISION,
				COD_PAGO_COMISION_TDNX)
			VALUES(
				@ve_cod_usuario_pago_comision,
				@ve_porc_comision,
				@ve_monto_comision,
				@ve_cod_pago_comision_tdnx) 
		end 
	else if (@ve_operacion='UPDATE') 
		begin
			UPDATE ITEM_US_PAGO_COMISION_TDNX
			SET COD_USUARIO_PAGO_COMISION			=	@ve_cod_usuario_pago_comision,
				PORC_COMISION						=	@ve_porc_comision,
				MONTO_COMISION						=	@ve_monto_comision,
				COD_PAGO_COMISION_TDNX				=	@ve_cod_pago_comision_tdnx				
			WHERE COD_ITEM_US_PAGO_COMISION_TDNX	=	@ve_cod_item_us_pago_comision_tdnx
		end	
	else if (@ve_operacion='DELETE') 
		begin
			DELETE  ITEM_US_PAGO_COMISION_TDNX 
    		WHERE COD_ITEM_US_PAGO_COMISION_TDNX	=	@ve_cod_item_us_pago_comision_tdnx
		end 
END
go