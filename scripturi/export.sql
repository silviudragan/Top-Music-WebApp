CREATE OR REPLACE DIRECTORY export_dir AS 'D:\Anul 2\Semestrul 2\PSGBD\Project';
GRANT READ ON DIRECTORY export_dir TO PUBLIC;

set SERVEROUTPUT ON;
DECLARE
  v_file UTL_FILE.FILE_TYPE;
  CURSOR c_tabel IS SELECT table_name FROM user_tables;
  CURSOR c_pachet IS SELECT object_name FROM ALL_OBJECTS WHERE OBJECT_TYPE IN ('PACKAGE') and OWNER='PROJECT';
  CURSOR c_trigger IS SELECT object_name FROM ALL_OBJECTS WHERE OBJECT_TYPE IN ('TRIGGER') and OWNER='PROJECT';
  CURSOR c_view IS SELECT object_name FROM ALL_OBJECTS WHERE OBJECT_TYPE IN ('VIEW') and OWNER='PROJECT';
  v_nume_entitate VARCHAR(50);
  v_creare_entitate CLOB;
BEGIN
  v_file := UTL_FILE.FOPEN('EXPORT_DIR', 'export_data.sql', 'W');
  
  -- Crearea tabelelor
  OPEN c_tabel;
  LOOP
    EXIT WHEN c_tabel%NOTFOUND;
    FETCH c_tabel into v_nume_entitate;
    SELECT DBMS_METADATA.GET_DDL('TABLE', v_nume_entitate) into v_creare_entitate FROM USER_TABLES where rownum <= 1;
    utl_file.put_line(v_file, v_creare_entitate);
  END LOOP;
  CLOSE c_tabel;
  
  -- Crearea triggerelor
  OPEN c_trigger;
  LOOP
    EXIT WHEN c_trigger%NOTFOUND;
    FETCH c_trigger into v_nume_entitate;
    SELECT DBMS_METADATA.GET_DDL('TRIGGER', v_nume_entitate) into v_creare_entitate FROM USER_TABLES where rownum <= 1;
    utl_file.put_line(v_file, v_creare_entitate);
  END LOOP;
  CLOSE c_trigger;
  
  -- Crearea pachetelor
  OPEN c_pachet;
  LOOP
    EXIT WHEN c_pachet%NOTFOUND;
    FETCH c_pachet into v_nume_entitate;
    SELECT DBMS_METADATA.GET_DDL('PACKAGE', v_nume_entitate) into v_creare_entitate FROM USER_TABLES where rownum <= 1;
    utl_file.put_line(v_file, v_creare_entitate);
  END LOOP;
  CLOSE c_pachet;
  
  -- Crearea view-urilor
  OPEN c_view;
  LOOP
    EXIT WHEN c_view%NOTFOUND;
    FETCH c_view into v_nume_entitate;
    SELECT DBMS_METADATA.GET_DDL('VIEW', v_nume_entitate) into v_creare_entitate FROM USER_TABLES where rownum <= 1;
    utl_file.put_line(v_file, v_creare_entitate);
  END LOOP;
  CLOSE c_view;
  
  UTL_FILE.FCLOSE(v_file);
END;