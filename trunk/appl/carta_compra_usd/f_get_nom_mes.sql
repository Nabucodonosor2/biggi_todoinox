ALTER FUNCTION [dbo].[f_get_nom_mes](@ve_mes  numeric)
RETURNS VARCHAR(10)
AS
BEGIN
	DECLARE @vs_nom_mes VARCHAR(10)
    SELECT @vs_nom_mes = NOM_MES
	FROM MES
    WHERE COD_MES = @ve_mes
	RETURN @vs_nom_mes
END
