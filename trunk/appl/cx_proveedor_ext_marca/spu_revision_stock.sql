CREATE PROCEDURE spu_revision_stock(@ve_operacion			VARCHAR(20),
								    @ve_cod_producto		VARCHAR(100)=NULL,
								    @ve_cod_proveedor_ext	NUMERIC=NULL,
									@ve_cantidad			NUMERIC=NULL)			
AS
BEGIN
	IF(@ve_operacion='INSERT') BEGIN
		 INSERT INTO REV_STOCK_CT(COD_PROVEEDOR_EXT
								,COD_PRODUCTO
								,CANTIDAD)
						  VALUES(@ve_cod_proveedor_ext
								,@ve_cod_producto
								,@ve_cantidad)	
	END
	ELSE IF(@ve_operacion='DELETE') BEGIN
		DELETE REV_STOCK_CT
		WHERE COD_PROVEEDOR_EXT = @ve_cod_proveedor_ext
	END
END