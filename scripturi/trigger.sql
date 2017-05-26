DROP TRIGGER sterge_date_aditionale;
CREATE or REPLACE TRIGGER sterge_date_aditionale
  AFTER DELETE ON users FOR EACH ROW
DECLARE
v_id INTEGER;
BEGIN
  v_id := :OLD.id;
  DELETE FROM COMMENTS where id_user = v_id;
  DELETE from songs where id_user = v_id;
  DELETE from votes where id_user = v_id;
END;