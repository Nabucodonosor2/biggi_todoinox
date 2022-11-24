CREATE PROCEDURE spu_cx_termino_pago(@ve_operacion varchar(20)
									,@ve_cod_cx_termino_pago numeric(10,0)
									,@ve_nom_cx_termino_pago varchar(100)
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_termino_pago (cod_cx_termino_pago
									,nom_cx_termino_pago)
		values (@ve_cod_cx_termino_pago
				,@ve_nom_cx_termino_pago)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_termino_pago 
		set nom_cx_termino_pago = @ve_nom_cx_termino_pago
	    where cod_cx_termino_pago = @ve_cod_cx_termino_pago
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_termino_pago 
    	where cod_cx_termino_pago = @ve_cod_cx_termino_pago
	end
END
go