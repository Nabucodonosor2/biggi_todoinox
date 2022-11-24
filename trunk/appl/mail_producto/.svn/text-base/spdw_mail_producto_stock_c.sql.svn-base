ALTER PROCEDURE [dbo].[spdw_mail_producto_stock_c]
AS
BEGIN
	DECLARE @TEMPO TABLE(
		COD_PRODUCTO			VARCHAR(30),
		NOM_PRODUCTO			VARCHAR(100),
		ULTIMO_30_DIAS			NUMERIC,
		ULTIMO_60_DIAS			NUMERIC,
		ULTIMO_90_DIAS			NUMERIC,
		PROMEDIO				NUMERIC(10,2),
		STOCK_ACTUAL			NUMERIC,
		MESES_STOCK				NUMERIC,
		FACTURACION_2013		NUMERIC
	)
		
	DECLARE
		@vc_cod_producto		VARCHAR(30),
		@vc_nom_producto		VARCHAR(100),
		@vc_stock				NUMERIC,
		@vc_facturacion_2013	NUMERIC,
		@vl_30_dias_f			NUMERIC,
		@vl_60_dias_f			NUMERIC,
		@vl_90_dias_f			NUMERIC,
		@vl_30_dias_nc			NUMERIC,
		@vl_60_dias_nc			NUMERIC,
		@vl_90_dias_nc			NUMERIC,
		@vl_sum_30_dias			NUMERIC,
		@vl_sum_60_dias			NUMERIC,
		@vl_sum_90_dias			NUMERIC,
		@vl_promedio			NUMERIC(10,2),
		@vl_meses_stock			NUMERIC
		
		
	DECLARE C_PRODUCTO CURSOR FOR
	SELECT COD_PRODUCTO
		  ,NOM_PRODUCTO
		  ,dbo.f_bodega_stock(COD_PRODUCTO, 1, getdate())
		  ,dbo.f_get_tot_factura_anterior(COD_PRODUCTO)
	FROM PRODUCTO
	WHERE MANEJA_STOCK_CRITICO = 'S'
	
	OPEN C_PRODUCTO
	FETCH C_PRODUCTO INTO @vc_cod_producto, @vc_nom_producto, @vc_stock, @vc_facturacion_2013
	WHILE @@FETCH_STATUS = 0 BEGIN
		
		SELECT @vl_30_dias_f = ISNULL(SUM(ITF.CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA ITF
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII IN (2, 3)
		AND F.FECHA_FACTURA BETWEEN DATEADD(DAY,-30, GETDATE()) AND GETDATE()
		AND F.COD_FACTURA = ITF.COD_FACTURA
		
		SELECT @vl_60_dias_f = ISNULL(SUM(ITF.CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA ITF
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII IN (2, 3)
		AND F.FECHA_FACTURA BETWEEN DATEADD(DAY,-60, GETDATE()) AND GETDATE()
		AND F.COD_FACTURA = ITF.COD_FACTURA
		
		SELECT @vl_90_dias_f = ISNULL(SUM(ITF.CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA ITF
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII IN (2, 3)
		AND F.FECHA_FACTURA BETWEEN DATEADD(DAY,-90, GETDATE()) AND GETDATE()
		AND F.COD_FACTURA = ITF.COD_FACTURA
		
		SELECT @vl_30_dias_nc = ISNULL(SUM(INC.CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII IN (2, 3)
		AND NC.FECHA_NOTA_CREDITO BETWEEN DATEADD(DAY,-30, GETDATE()) AND GETDATE()
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO
		
		SELECT @vl_60_dias_nc = ISNULL(SUM(INC.CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII IN (2, 3)
		AND NC.FECHA_NOTA_CREDITO BETWEEN DATEADD(DAY,-60, GETDATE()) AND GETDATE()
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO
		
		SELECT @vl_90_dias_nc = ISNULL(SUM(INC.CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII IN (2, 3)
		AND NC.FECHA_NOTA_CREDITO BETWEEN DATEADD(DAY,-90, GETDATE()) AND GETDATE()
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO
		
		SET @vl_sum_30_dias = @vl_30_dias_f - @vl_30_dias_nc
		SET @vl_sum_60_dias = @vl_60_dias_f - @vl_60_dias_nc
		SET @vl_sum_90_dias = @vl_90_dias_f - @vl_90_dias_nc

		SET @vl_promedio = ROUND(((@vl_sum_30_dias + @vl_sum_60_dias + @vl_sum_90_dias)/3), 2, 1)
		
		if(@vl_promedio <> 0)
			SET @vl_meses_stock = ROUND(@vc_stock/@vl_promedio, 0, 1)
		else
			SET @vl_meses_stock = ROUND(@vc_stock, 0, 1)	
		
		INSERT INTO @TEMPO VALUES(@vc_cod_producto
								 ,@vc_nom_producto
								 ,@vl_sum_30_dias
								 ,@vl_sum_60_dias
								 ,@vl_sum_90_dias
								 ,@vl_promedio
								 ,@vc_stock
								 ,@vl_meses_stock
								 ,@vc_facturacion_2013)
		
		FETCH C_PRODUCTO INTO @vc_cod_producto, @vc_nom_producto, @vc_stock, @vc_facturacion_2013
	END
	CLOSE C_PRODUCTO
	DEALLOCATE C_PRODUCTO
	
	SELECT * FROM @TEMPO
	ORDER BY COD_PRODUCTO
END