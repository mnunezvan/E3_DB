CREATE OR REPLACE FUNCTION actualiza_puntos()
RETURNS TRIGGER AS $$
BEGIN
  UPDATE usuario U
     SET puntos = U.puntos
                + COALESCE(
                    (SELECT SUM(r.monto)/1000.0
                       FROM reserva r
                      WHERE r.agenda_id = NEW.id
                    ),0)
   WHERE U.correo = NEW.correo_usuario;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER trg_actualiza_puntos
AFTER INSERT ON agenda
FOR EACH ROW
EXECUTE PROCEDURE actualiza_puntos();
