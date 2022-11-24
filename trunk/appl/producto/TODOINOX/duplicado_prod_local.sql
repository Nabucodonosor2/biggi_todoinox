ALTER PROCEDURE DUPLICADOS_PROD_LOCAL 
AS 
BEGIN 
		DECLARE 
		@VC_COD_PRODUCTO_LOCAL NUMERIC,
		@VC_COD_PRODUCTO	VARCHAR(30),
		@VC_ES_COMPUESTO	VARCHAR(1),
		@VL_COD_PRODUCTO_LOCAL NUMERIC,
		@VL_COUNT				NUMERIC

		declare C_PRO_LOCAL cursor for
		
		SELECT *
		FROM PRODUCTO_LOCAL
		WHERE COD_PRODUCTO IN (SELECT COD_PRODUCTO
								 FROM PRODUCTO_LOCAL
							 GROUP BY COD_PRODUCTO
							 HAVING COUNT(*) > 1)
		ORDER BY COD_PRODUCTO
		
	OPEN C_PRO_LOCAL
	FETCH C_PRO_LOCAL INTO @VC_COD_PRODUCTO_LOCAL, @VC_COD_PRODUCTO, @VC_ES_COMPUESTO
	WHILE @@FETCH_STATUS = 0 BEGIN
				
				
				SELECT  @VL_COUNT = COUNT(COD_PRODUCTO_LOCAL)
				FROM PRODUCTO_LOCAL 
				WHERE COD_PRODUCTO = @VC_COD_PRODUCTO
				
				IF(@VL_COUNT != 1)BEGIN	
				SELECT  @VL_COD_PRODUCTO_LOCAL = max(COD_PRODUCTO_LOCAL)
				FROM PRODUCTO_LOCAL 
				WHERE COD_PRODUCTO = @VC_COD_PRODUCTO
				
				DELETE PRODUCTO_LOCAL
				WHERE COD_PRODUCTO_LOCAL =@VL_COD_PRODUCTO_LOCAL
				END
					
	FETCH C_PRO_LOCAL INTO @VC_COD_PRODUCTO_LOCAL, @VC_COD_PRODUCTO, @VC_ES_COMPUESTO
	END
	CLOSE C_PRO_LOCAL
	DEALLOCATE C_PRO_LOCAL
END 