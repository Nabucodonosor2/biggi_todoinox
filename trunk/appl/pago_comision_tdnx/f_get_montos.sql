-----------------------------  f_get_montos ------------------
CREATE FUNCTION f_get_montos(@ve_tipo_doc		varchar(10)
						   ,@ve_año				numeric
						   ,@ve_fecha_entrada	datetime
						   ,@ve_fecha_hasta		datetime)
RETURNS numeric
AS
BEGIN
	declare @vl_result		numeric
	
	if(@ve_tipo_doc = 'OTROS' AND @ve_año = 2014)BEGIN
		SELECT @vl_result = ISNULL(SUM(TOTAL_NETO), 0)
		FROM FACTURA F
			,EMPRESA E
		WHERE COD_ESTADO_DOC_SII IN (2, 3)
		AND FECHA_FACTURA between DATEADD(YEAR, -1, @ve_fecha_entrada) and DATEADD(YEAR, -1, @ve_fecha_hasta)
		AND E.EMP_RELACIONADA_COMISION_TDNX = 0
		AND F.COD_EMPRESA = E.COD_EMPRESA
	END
	ELSE IF(@ve_tipo_doc = 'OTROS' AND @ve_año = 2015)BEGIN
		SELECT @vl_result = ISNULL(SUM(TOTAL_NETO), 0)
		FROM FACTURA F
			,EMPRESA E
		WHERE COD_ESTADO_DOC_SII IN (2, 3)
		AND FECHA_FACTURA between @ve_fecha_entrada and @ve_fecha_hasta
		AND E.EMP_RELACIONADA_COMISION_TDNX = 0
		AND F.COD_EMPRESA = E.COD_EMPRESA
	END
	ELSE IF(@ve_tipo_doc = 'NC' AND @ve_año = 2014)BEGIN
		SELECT @vl_result = ISNULL(SUM(TOTAL_NETO), 0)
		FROM NOTA_CREDITO NC
			,EMPRESA E
		WHERE COD_ESTADO_DOC_SII IN (2, 3)
		AND FECHA_NOTA_CREDITO between DATEADD(YEAR, -1, @ve_fecha_entrada) and DATEADD(YEAR, -1, @ve_fecha_hasta)
		AND E.EMP_RELACIONADA_COMISION_TDNX = 0
		AND NC.COD_EMPRESA = E.COD_EMPRESA
	END
	ELSE IF(@ve_tipo_doc = 'NC' AND @ve_año = 2015)BEGIN
		SELECT @vl_result = ISNULL(SUM(TOTAL_NETO), 0)
		FROM NOTA_CREDITO NC
			,EMPRESA E
		WHERE COD_ESTADO_DOC_SII IN (2, 3)
		AND FECHA_NOTA_CREDITO between @ve_fecha_entrada and @ve_fecha_hasta
		AND E.EMP_RELACIONADA_COMISION_TDNX = 0
		AND NC.COD_EMPRESA = E.COD_EMPRESA
	END

	return @vl_result
END