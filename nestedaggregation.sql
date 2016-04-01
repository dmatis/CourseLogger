drop table task_average; 

create table task_average as select task_id, avg(time_spent) as avgtime from (select p.task_id, p.time_spent from (select task_id, time_spent from performs union all select task_id, time_spent from group_performs) p, task t where p.task_id = t.task_id and t.course_dept like 'CPSC' and t.course_num = 404) group by task_id;

select t.task_id, t.descrip from task_average ta, task t where ta.task_id = t.task_id and ta.avgtime = (select max(avgtime) from task_average);