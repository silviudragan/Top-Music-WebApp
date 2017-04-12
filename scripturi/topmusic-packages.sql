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
DROP PACKAGE stats_page;
/

CREATE OR REPLACE PACKAGE stats_page AS
  
  FUNCTION get_id(p_username users.username%TYPE) RETURN users.id%TYPE;
  PROCEDURE distributie(p_username IN users.username%TYPE, p_search_type IN INTEGER, cursor_output OUT SYS_REFCURSOR);
  PROCEDURE lucky(p_username IN users.username%TYPE, p_number IN INTEGER, cursor_output OUT SYS_REFCURSOR);
  
END;
/

CREATE OR REPLACE PACKAGE BODY stats_page AS
  
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
  
  -- Procedura ce ii recomanda user-ului 10 piese cele mai populare si cele mai apropiate de gustul lui pe care nu le-a votat inca
  PROCEDURE lucky(p_username IN users.username%TYPE, p_number IN INTEGER, cursor_output OUT SYS_REFCURSOR) AS
    v_id_user users.id%TYPE := get_id(p_username);
  BEGIN
    
    OPEN cursor_output FOR
      SELECT UNIQUE s.id_song, s.name, s.votes FROM songs s, users u, votes v, genres g, song_genre sg
      WHERE s.id_song = v.id_song AND NOT v.id_user = u.id AND u.id = v_id_user AND g.id_genre = sg.id_genre AND sg.id_song = v.id_song
      AND g.id_genre IN (SELECT g.id_genre FROM users u, genres g, songs s, song_genre sg WHERE g.id_genre = sg.id_genre AND sg.id_song = s.id_song
      AND s.id_user = u.id AND u.id = v_id_user) AND rownum <= TRUNC(DBMS_RANDOM.value(1, p_number)) ORDER BY s.votes DESC;
  END lucky;
END;
