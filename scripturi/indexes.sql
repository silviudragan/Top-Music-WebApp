set serveroutput on;

-- First index on ORDER BY clause
drop index first_index;

create index first_index
on songs(votes DESC, posted_time DESC);

select * from songs where posted_time >= TO_DATE('01-JAN-88', 'dd-mm-yyyy') order by votes DESC, posted_time DESC;

-- Second index on GROUP BY clause
drop index second_index;

create index second_index
on songs(id_user, votes);

select id_user, votes from songs where id_user > 100 group by id_user, votes;

-- Third index on JOIN clause
drop index third_index;

create index third_index
on comments(comm, posted_time);

select comm, posted_time from comments c, users u where c.id_user = u.id;
