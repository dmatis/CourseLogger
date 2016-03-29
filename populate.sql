CREATE table student (
	snum number,
	password varchar2(20),
	fname varchar2(20),
	lname varchar2(20),
	major varchar2(4),
	primary key(snum));

CREATE table professor (
	profid number,
	dept varchar2(4),
	primary key(profid));

CREATE table group_project (
	task_id number,
	snum number,
	max_size number,
	deadline date,
	descrip varchar2(30),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id, snum));

CREATE table course_teach (
	course_num number,
	course_dept, varchar2(4),
	profid number,
	primary key(course_num, course_dept),
	foreign key(profid) references professor);

CREATE table assignment (
	task_id number,
	deadline date,
	descrip varchar2(30),
	hand_in_loc varchar2(10),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id),
	foreign key(snum) references student,
	foreign key(course_num, course_dept) references course_teach);

CREATE table exam (
	task_id number,
	deadline date,
	descrip varchar2(30),
	cheatsheet varchar2(1),
	location varchar2(10),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id),
	foreign key(course_num, course_dept) references course_teach);

CREATE table performs (
	snum number,
	task_id number,
	time_spent varchar2(4),
	completed varchar2(1),
	grade number,
	primary key(snum, task_id),
	foreign key(snum) references student,
		on delete cascade,
		on update cascade,
	foreign key(task_id) references task,
		on delete cascade,
		on update cascade);
