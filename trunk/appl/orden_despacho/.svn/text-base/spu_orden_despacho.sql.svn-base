ALTER PROCEDURE [dbo].[spu_orden_despacho](@ve_operacion					varchar(30)
					    		   ,@ve_cod_orden_despacho			numeric(10)=NULL
					    		   ,@ve_fecha_orden_despacho		datetime=NULL	
								   ,@ve_cod_usuario					numeric(10)=NULL
								   ,@ve_cod_doc_origen				numeric(10)=NULL
								   ,@ve_tipo_doc_origen				varchar(100)=NULL
								   ,@ve_referencia					varchar(100)=NULL
								   ,@ve_obs							text=NULL
								   ,@ve_cod_usuario_anula			numeric(10)=NULL
								   ,@ve_motivo_anula				varchar(100)=NULL
								   ,@ve_cod_empresa					numeric(10)=NULL
								   ,@ve_rut							numeric(10)=NULL
								   ,@ve_dig_verif					varchar(1)=NULL
								   ,@ve_nom_empresa					varchar(100)=NULL
								   ,@ve_giro						varchar(100)=NULL
								   ,@ve_cod_usuario_impresion		numeric(10)=NULL
								   ,@ve_cod_usuario_vendedor1		numeric(10)=NULL
								   ,@ve_cod_usuario_vendedor2		numeric(10)=NULL
								   ,@ve_cod_usuario_despacha		numeric(10)=NULL
								   ,@recibido_por					numeric(10) = null
								   ,@es_recibido_por				varchar(1) = null)
AS
BEGIN
	DECLARE
		@vl_fecha_anula datetime,
		@vl_cod_log_cambio numeric,
		@vl_cod_estado_orden_despacho numeric
	
	if (@ve_operacion='INSERT') begin
		if(@ve_cod_usuario_anula IS NOT NULL)
			set @vl_fecha_anula = GETDATE()
		
		INSERT INTO ORDEN_DESPACHO(FECHA_REGISTRO
							      ,COD_DOC_ORIGEN
							      ,TIPO_DOC_ORIGEN
							      ,FECHA_ORDEN_DESPACHO
							      ,REFERENCIA
							      ,OBS
							      ,COD_USUARIO_ANULA
							      ,FECHA_ANULA
							      ,MOTIVO_ANULA
							      ,COD_EMPRESA
							      ,RUT
							      ,DIG_VERIF
							      ,NOM_EMPRESA
							      ,GIRO
							      ,COD_USUARIO_IMPRESION
							      ,COD_USUARIO_VENDEDOR1
							      ,COD_USUARIO_VENDEDOR2
							      ,COD_USUARIO
							      ,COD_USUARIO_DESPACHA
							      ,ES_RECIBIDO_POR)
							VALUES(GETDATE()
							      ,@ve_cod_doc_origen
							      ,@ve_tipo_doc_origen
							      ,@ve_fecha_orden_despacho
							      ,@ve_referencia
							      ,@ve_obs
							      ,@ve_cod_usuario_anula
							      ,@vl_fecha_anula
							      ,@ve_motivo_anula
							      ,@ve_cod_empresa
							      ,@ve_rut
							      ,@ve_dig_verif
							      ,@ve_nom_empresa
							      ,@ve_giro
							      ,@ve_cod_usuario_impresion
							      ,@ve_cod_usuario_vendedor1
							      ,@ve_cod_usuario_vendedor2
							      ,@ve_cod_usuario
							      ,@ve_cod_usuario_despacha
							      ,@es_recibido_por)      
	end 
	if (@ve_operacion='UPDATE') begin
		if(@ve_cod_usuario_anula IS NOT NULL)
			set @vl_fecha_anula = GETDATE()
		
		UPDATE ORDEN_DESPACHO
		SET COD_DOC_ORIGEN			= @ve_cod_doc_origen
		,TIPO_DOC_ORIGEN			= @ve_tipo_doc_origen
		,FECHA_ORDEN_DESPACHO		= @ve_fecha_orden_despacho
		,REFERENCIA					= @ve_referencia
		,OBS						= @ve_obs
		,COD_USUARIO_ANULA			= @ve_cod_usuario_anula
		,FECHA_ANULA				= @vl_fecha_anula
		,MOTIVO_ANULA				= @ve_motivo_anula
		,COD_EMPRESA				= @ve_cod_empresa
		,RUT						= @ve_rut
		,DIG_VERIF					= @ve_dig_verif
		,NOM_EMPRESA				= @ve_nom_empresa
		,GIRO						= @ve_giro
		,COD_USUARIO_IMPRESION		= @ve_cod_usuario_impresion
		,COD_USUARIO_VENDEDOR1		= @ve_cod_usuario_vendedor1
		,COD_USUARIO_VENDEDOR2		= @ve_cod_usuario_vendedor2
		,COD_USUARIO_DESPACHA		= @ve_cod_usuario_despacha
		,RECIBIDO_POR				= @recibido_por
		,ES_RECIBIDO_POR			= @es_recibido_por
		WHERE COD_ORDEN_DESPACHO = @ve_cod_orden_despacho
	end
	if (@ve_operacion='PRINT') begin
		select @vl_cod_estado_orden_despacho = COD_ESTADO_ORDEN_DESPACHO
		from orden_despacho
		WHERE COD_ORDEN_DESPACHO = @ve_cod_orden_despacho	
		
		if(@vl_cod_estado_orden_despacho <> 3)begin
			UPDATE ORDEN_DESPACHO
			SET COD_ESTADO_ORDEN_DESPACHO = 3 --ENTREGADA
			WHERE COD_ORDEN_DESPACHO = @ve_cod_orden_despacho
			
			INSERT INTO LOG_CAMBIO VALUES (@ve_cod_usuario, 'ORDEN_DESPACHO', @ve_cod_orden_despacho, GETDATE(), 'U')		
			set @vl_cod_log_cambio = @@identity
			INSERT INTO DETALLE_CAMBIO VALUES (@vl_cod_log_cambio, 'COD_ESTADO_ORDEN_DESPACHO', @vl_cod_estado_orden_despacho, '3')
		end
	end
	if (@ve_operacion='ANULA_OD') begin
		select @vl_cod_estado_orden_despacho = COD_ESTADO_ORDEN_DESPACHO
		from orden_despacho
		WHERE COD_ORDEN_DESPACHO = @ve_cod_orden_despacho
		
		UPDATE ORDEN_DESPACHO
		SET COD_ESTADO_ORDEN_DESPACHO = 4 --ANULADA
		WHERE COD_ORDEN_DESPACHO = @ve_cod_orden_despacho
		
		INSERT INTO LOG_CAMBIO VALUES (@ve_cod_usuario, 'ORDEN_DESPACHO', @ve_cod_orden_despacho, GETDATE(), 'U')		
		set @vl_cod_log_cambio = @@identity
		INSERT INTO DETALLE_CAMBIO VALUES (@vl_cod_log_cambio, 'COD_ESTADO_ORDEN_DESPACHO', @vl_cod_estado_orden_despacho, '4')
	end 
END