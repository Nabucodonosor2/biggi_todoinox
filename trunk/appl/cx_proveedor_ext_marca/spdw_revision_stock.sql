ALTER PROCEDURE spdw_revision_stock(@ve_cod_proveedor_ext numeric(18))
AS
BEGIN
	DECLARE @TEMPO TABLE(
		COD_PRODUCTO			VARCHAR(100),
		STOCK					NUMERIC,
		NEXT_CORRELATIVO		NUMERIC,
		STOCK_NOMINAL			NUMERIC,
		VENTAS_TRES				NUMERIC,
		VENTAS_DOS				NUMERIC,
		VENTAS_UNO				NUMERIC,
		VENTAS_HOY				NUMERIC
	)

	DECLARE 
		@vc_cod_producto		varchar(100),
		@vc_stock				numeric,
		@vl_next_correlativo	numeric,
		@vl_stock_nominal		numeric,
		@vl_ventas_1			numeric,
		@vl_ventas_2			numeric,
		@vl_ventas_3			numeric,
		@vl_nc_ventas_1			numeric,
		@vl_nc_ventas_2			numeric,
		@vl_nc_ventas_3			numeric,
		@vl_ventas_hoy			numeric,
		@vl_nc_ventas_hoy		numeric
	
	-- declara cursor
	DECLARE C_CURSOR CURSOR FOR
	SELECT TOP 20 COD_PRODUCTO
		  ,dbo.f_bodega_stock(COD_PRODUCTO, 1, GETDATE())
	FROM CX_OC_EXTRANJERA COE
		,CX_ITEM_OC_EXTRANJERA COC
	WHERE COD_PROVEEDOR_EXT = @ve_cod_proveedor_ext
	AND COE.COD_CX_OC_EXTRANJERA = COC.COD_CX_OC_EXTRANJERA
	GROUP BY COD_PRODUCTO
	ORDER BY COD_PRODUCTO ASC
		
	-- abre cursor
	OPEN C_CURSOR
	FETCH C_CURSOR INTO @vc_cod_producto, @vc_stock	
	WHILE @@FETCH_STATUS = 0 BEGIN
		
		SELECT @vl_next_correlativo = ISNULL(SUM(CANTIDAD), 0)
		FROM CX_OC_EXTRANJERA COE
			,CX_ITEM_OC_EXTRANJERA COC
		WHERE COD_PROVEEDOR_EXT = @ve_cod_proveedor_ext
		AND COD_PRODUCTO = @vc_cod_producto
		AND COE.ETA_DATE > GETDATE()
		AND COE.COD_CX_OC_EXTRANJERA = COC.COD_CX_OC_EXTRANJERA

		SET @vl_stock_nominal = @vc_stock + @vl_next_correlativo

		SELECT @vl_ventas_3 = ISNULL(SUM(CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA IFA
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_FACTURA) = YEAR(GETDATE()) - 3
		AND F.COD_FACTURA = IFA.COD_FACTURA

		SELECT @vl_nc_ventas_3 = ISNULL(SUM(CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_NOTA_CREDITO) = YEAR(GETDATE()) - 3
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO

		SET @vl_ventas_3 = @vl_ventas_3 - @vl_nc_ventas_3

		SELECT @vl_ventas_2 = ISNULL(SUM(CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA IFA
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_FACTURA) = YEAR(GETDATE()) - 2
		AND F.COD_FACTURA = IFA.COD_FACTURA

		SELECT @vl_nc_ventas_2 = ISNULL(SUM(CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_NOTA_CREDITO) = YEAR(GETDATE()) - 2
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO

		SET @vl_ventas_2 = @vl_ventas_2 - @vl_nc_ventas_2

		SELECT @vl_ventas_1 = ISNULL(SUM(CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA IFA
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_FACTURA) = YEAR(GETDATE()) - 1
		AND F.COD_FACTURA = IFA.COD_FACTURA

		SELECT @vl_nc_ventas_1 = ISNULL(SUM(CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_NOTA_CREDITO) = YEAR(GETDATE()) - 1
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO

		SET @vl_ventas_1 = @vl_ventas_1 - @vl_nc_ventas_1

		SELECT @vl_ventas_hoy = ISNULL(SUM(CANTIDAD), 0)
		FROM FACTURA F
			,ITEM_FACTURA IFA
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND F.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_FACTURA) = YEAR(GETDATE())
		AND F.COD_FACTURA = IFA.COD_FACTURA

		SELECT @vl_nc_ventas_hoy = ISNULL(SUM(CANTIDAD), 0)
		FROM NOTA_CREDITO NC
			,ITEM_NOTA_CREDITO INC
		WHERE COD_PRODUCTO = @vc_cod_producto
		AND NC.COD_ESTADO_DOC_SII = 3
		AND YEAR(FECHA_NOTA_CREDITO) = YEAR(GETDATE())
		AND NC.COD_NOTA_CREDITO = INC.COD_NOTA_CREDITO

		SET @vl_ventas_hoy = @vl_ventas_hoy - @vl_nc_ventas_hoy

		INSERT INTO @TEMPO (COD_PRODUCTO,		STOCK,		NEXT_CORRELATIVO,		STOCK_NOMINAL,		VENTAS_TRES,	VENTAS_DOS,		VENTAS_UNO,		VENTAS_HOY) 
					VALUES (@vc_cod_producto,	@vc_stock,	@vl_next_correlativo,	@vl_stock_nominal,	@vl_ventas_3,	@vl_ventas_2,	@vl_ventas_1,	@vl_ventas_hoy)

		FETCH C_CURSOR INTO  @vc_cod_producto, @vc_stock	 
	END
	CLOSE C_CURSOR
	DEALLOCATE C_CURSOR


	SELECT COD_PRODUCTO
		  ,STOCK
		  ,NEXT_CORRELATIVO
		  ,STOCK_NOMINAL
		  ,VENTAS_TRES
		  ,VENTAS_DOS
		  ,VENTAS_UNO
		  ,VENTAS_HOY
	FROM @TEMPO	
END