drop table performs_each;
drop table performs_each_group;
drop table performs_total;

create table performs_each as select stid 
from performs 
where task_id in (select task_id from task where course_num='304' and course_dept='CPSC') and completed='Y' 
group by stid having count(*) = (select count(*) 
                                 from task 
                                 where course_num='304' and course_dept='CPSC');

create table performs_each_group as select stid
from group_performs 
where task_id in (select task_id from task where course_num='304' and course_dept='CPSC') and completed='Y' 
group by stid having count(*) = (select count(*) 
                                 from task 
                                 where course_num='304' and course_dept='CPSC');
                    

create table performs_total as select performs_each.stid 
from performs_each
left join performs_each_group
on performs_each.stid=performs_each_group.stid;

select fname, lname
from student S, performs_total PT
where S.stid = PT.stid;
