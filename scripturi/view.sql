--CREATE MATERIALIZED VIEW users_comments AS
--  select u.id, comm, posted_time from comments c, users u 
--  where c.id_user = u.id and length(comm) > 5 and posted_time >= TO_DATE('01-10-2015', 'dd-mm-yyyy');
--
--DROP MATERIALIZED VIEW users_comments;
  
CREATE MATERIALIZED VIEW comments_view FOR UPDATE AS
  select id_comm, id_user, comm, posted_time from comments c 
  where length(comm) > 5 and posted_time >= TO_DATE('01-10-2015', 'dd-mm-yyyy')
  order by posted_time desc;

DROP MATERIALIZED VIEW comments_view;

SELECT * FROM comments_view;
select * from comments_view where id_comm = 3001;
select max(id_comm) from comments_view;

INSERT INTO comments_view VALUES(3000, 177, 'Tralalala', SYSDATE);

INSERT INTO comments_view VALUES(3001, 177, 'Meow', SYSDATE);
UPDATE comments_view SET comm = 'Meow-Meow' WHERE id_comm = 3001;

DELETE FROM comments_view WHERE id_comm = 3000;
DELETE FROM comments_view WHERE id_comm = 3001;
