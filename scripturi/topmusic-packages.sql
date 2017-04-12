set serveroutput on;
/
DROP PACKAGE testare_procedura;
/
CREATE OR REPLACE PACKAGE testare_procedura AS
  PROCEDURE afisare(p_text IN VARCHAR2, p_output OUT VARCHAR2);
END;
/

CREATE OR REPLACE PACKAGE BODY testare_procedura AS
  PROCEDURE afisare(p_text IN VARCHAR2, p_output OUT VARCHAR2) IS
  
  BEGIN
    p_output := 'Hello World!' || p_text;
    DBMS_OUTPUT.PUT_LINE(p_output);
  END afisare;
END;
/
----------------------

-- Package ce contine functii ce fac calcule pe partea de PL/SQL. Vor fi apelate simplu din PHP
DROP PACKAGE server_procedures;
/

CREATE OR REPLACE PACKAGE server_procedures AS
  
  FUNCTION get_id(p_username users.username%TYPE) RETURN users.id%TYPE;
  PROCEDURE distributie(p_username IN users.username%TYPE, p_search_type IN INTEGER, cursor_output OUT SYS_REFCURSOR);
  
END;
/

CREATE OR REPLACE PACKAGE BODY server_procedures AS
  
  FUNCTION get_id(p_username users.username%TYPE) RETURN users.id%TYPE AS
    v_id users.id%TYPE := 0;
  BEGIN
    SELECT id INTO v_id FROM users WHERE username = p_username;
    RETURN v_id;
  END get_id;
  
  PROCEDURE distributie(p_username IN users.username%TYPE, p_search_type IN INTEGER, cursor_output OUT SYS_REFCURSOR) AS 
  v_id_user users.id%TYPE := get_id(p_username);
  v_numar_piese_postate INTEGER := 0;
  v_numar_total_piese_votate INTEGER := 0;
  
  BEGIN
    SELECT count(s.id_song) INTO v_numar_piese_postate FROM songs s, users u WHERE s.id_user = u.id AND u.id = v_id_user;
    SELECT count(v.id_user) INTO v_numar_total_piese_votate FROM votes v, users u WHERE v.id_user = u.id AND u.id = v_id_user;
    
    IF(p_search_type = 1) THEN
      OPEN cursor_output FOR
        SELECT g.name, TRUNC(count(s.id_song)/v_numar_piese_postate * 100, 2) || '%', count(s.id_song) FROM songs s, genres g, song_genre sg, users u 
        WHERE s.id_song = sg.id_song AND g.id_genre = sg.id_genre AND s.id_user = u.id AND u.id = v_id_user
        GROUP BY g.name;
        
    ELSIF(p_search_type = 2) THEN
      OPEN cursor_output FOR
        SELECT g.name, TRUNC(count(v.id_user)/v_numar_total_piese_votate * 100, 2) || '%', count(v.id_user) FROM songs s, genres g, song_genre sg, users u, votes v 
        WHERE s.id_song = sg.id_song AND g.id_genre = sg.id_genre AND v.id_song = s.id_song AND v.id_user = u.id AND u.id = v_id_user AND v.voted = 1
        GROUP BY g.name;
        
    ELSIF(p_search_type = 3) THEN
      OPEN cursor_output FOR
        SELECT g.name, TRUNC(count(v.id_user)/v_numar_total_piese_votate * 100, 2) || '%', count(v.id_user) FROM songs s, genres g, song_genre sg, users u, votes v 
        WHERE s.id_song = sg.id_song AND g.id_genre = sg.id_genre AND v.id_song = s.id_song AND v.id_user = u.id AND u.id = v_id_user AND v.voted = 0
        GROUP BY g.name;
    END IF;
    
  END distributie;
  
END;
