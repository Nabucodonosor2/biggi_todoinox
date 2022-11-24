CREATE PROCEDURE spu_cx_clausula_compra	(@ve_operacion varchar(20)
										,@ve_cod_cx_clausula_compra numeric(10,0)
										,@ve_nom_cx_clausula_compra varchar(100))
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_clausula_compra (cod_cx_clausula_compra
										,nom_cx_clausula_compra)
		values (@ve_cod_cx_clausula_compra
				,@ve_nom_cx_clausula_compra)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_clausula_compra 
		set nom_cx_clausula_compra = @ve_nom_cx_clausula_compra
	    where cod_cx_clausula_compra = @ve_cod_cx_clausula_compra
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_clausula_compra 
    	where cod_cx_clausula_compra = @ve_cod_cx_clausula_compra
	end
END
go
