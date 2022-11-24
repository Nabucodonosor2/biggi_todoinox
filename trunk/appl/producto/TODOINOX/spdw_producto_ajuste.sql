CREATE PROCEDURE spdw_producto_ajuste(@ve_cod_producto VARCHAR(100))
AS
BEGIN
	DECLARE @TEMPO TABLE(
		TIPO_MOVIMIENTO			VARCHAR(100),
		COD_MOVIMIENTO			NUMERIC,
		FECHA_MOVIMIENTO		DATETIME,
		CANTIDAD_MOVIMIENTO		NUMERIC
	)
	
	DECLARE
		@vl_cod_entrada_bodega		NUMERIC,
		@vl_fecha_entrada_bodega	DATETIME,
		@vl_cantidad_entrada		NUMERIC,
		@vl_cod_salida_bodega		NUMERIC,
		@vl_fecha_salida_bodega		DATETIME,
		@vl_cantidad_salida			NUMERIC
		
	SELECT TOP 1 @vl_cod_entrada_bodega = EB.COD_ENTRADA_BODEGA
				,@vl_fecha_entrada_bodega = EB.FECHA_ENTRADA_BODEGA
				,@vl_cantidad_entrada = IEB.CANTIDAD
	FROM ENTRADA_BODEGA EB
		,ITEM_ENTRADA_BODEGA IEB
	WHERE IEB.COD_PRODUCTO = @ve_cod_producto
	AND EB.TIPO_DOC = 'AJUSTE' 
	AND EB.COD_ENTRADA_BODEGA = IEB.COD_ENTRADA_BODEGA
	ORDER BY EB.FECHA_ENTRADA_BODEGA DESC
	
	SELECT TOP 1 @vl_cod_salida_bodega = SB.COD_SALIDA_BODEGA
				,@vl_fecha_salida_bodega = SB.FECHA_SALIDA_BODEGA
				,@vl_cantidad_salida = ISB.CANTIDAD
	FROM SALIDA_BODEGA SB
		,ITEM_SALIDA_BODEGA ISB
	WHERE ISB.COD_PRODUCTO = @ve_cod_producto
	AND SB.TIPO_DOC = 'AJUSTE' 
	AND SB.COD_SALIDA_BODEGA = ISB.COD_SALIDA_BODEGA
	ORDER BY SB.FECHA_SALIDA_BODEGA DESC
	
	IF(@vl_cod_entrada_bodega IS NULL AND @vl_cod_salida_bodega IS NOT NULL)
		INSERT INTO @TEMPO 
		VALUES ('SALIDA'
				,@vl_cod_salida_bodega
				,@vl_fecha_salida_bodega
				,@vl_cantidad_salida)
			   
	ELSE IF (@vl_cod_salida_bodega IS NULL AND @vl_cod_entrada_bodega IS NOT NULL)
		INSERT INTO @TEMPO 
		VALUES ('ENTRADA'
				,@vl_cod_entrada_bodega
				,@vl_fecha_entrada_bodega
				,@vl_cantidad_entrada)
	ELSE if(@vl_cod_entrada_bodega IS NOT NULL AND @vl_cod_salida_bodega IS NOT NULL)
		IF(@vl_fecha_entrada_bodega > @vl_fecha_salida_bodega)
			INSERT INTO @TEMPO 
			VALUES ('ENTRADA'
					,@vl_cod_entrada_bodega
					,@vl_fecha_entrada_bodega
					,@vl_cantidad_entrada)
		ELSE	
			INSERT INTO @TEMPO 
			VALUES ('SALIDA'
					,@vl_cod_salida_bodega
					,@vl_fecha_salida_bodega
					,@vl_cantidad_salida)
			
	SELECT TIPO_MOVIMIENTO
		  ,COD_MOVIMIENTO
		  ,CONVERT(VARCHAR, FECHA_MOVIMIENTO, 103) FECHA_MOVIMIENTO
		  ,CANTIDAD_MOVIMIENTO 
	FROM @TEMPO			
END