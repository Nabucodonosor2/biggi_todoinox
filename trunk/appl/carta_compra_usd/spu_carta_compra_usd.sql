ALTER PROCEDURE [dbo].[spu_carta_compra_usd]
			(@ve_operacion					varchar(20) = null
			,@ve_cod_cx_carta_compra_usd	numeric = null
			,@ve_cod_usuario				numeric = null
			,@ve_cod_estado_carta_compra	numeric = null
			,@ve_atencion					varchar(100) = null
			,@ve_referencia					varchar(100) = null
			,@ve_tipo_cambio_usd			numeric(18,2) = null
			,@ve_cant_compra_usd			numeric = null
			,@ve_total_debito_pesos			numeric = null
			,@ve_fecha_cx_carta_compra_usd	date = null)
			AS
	BEGIN
		if (@ve_operacion='UPDATE') 
			begin
				UPDATE cx_carta_compra_usd		
				SET			cod_estado_carta_compra		=	@ve_cod_estado_carta_compra	 
							,atencion					=	@ve_atencion
							,referencia					=	@ve_referencia	
							,tipo_cambio_usd			=	@ve_tipo_cambio_usd	
							,cant_compra_usd			=	@ve_cant_compra_usd	
							,total_debito_pesos			=	@ve_total_debito_pesos
							,fecha_cx_carta_compra_usd	=	@ve_fecha_cx_carta_compra_usd		
				WHERE cod_cx_carta_compra_usd = @ve_cod_cx_carta_compra_usd
			end
		else if (@ve_operacion='INSERT') 
			begin
				insert into cx_carta_compra_usd
					(cod_usuario
					,cod_estado_carta_compra
					,atencion
					,referencia
					,tipo_cambio_usd
					,cant_compra_usd
					,total_debito_pesos
					,fecha_cx_carta_compra_usd
					,fecha_registro)
				values 
					(@ve_cod_usuario
					,@ve_cod_estado_carta_compra
					,@ve_atencion	
					,@ve_referencia	
					,@ve_tipo_cambio_usd	
					,@ve_cant_compra_usd	
					,@ve_total_debito_pesos	
					,@ve_fecha_cx_carta_compra_usd
					,getdate())	
			end 
		else if(@ve_operacion='DELETE')
		begin
			delete from cx_carta_compra_usd
			WHERE cod_cx_carta_compra_usd = @ve_cod_cx_carta_compra_usd 
		end
END