DROP TABLE student CASCADE CONSTRAINTS;
DROP TABLE professor CASCADE CONSTRAINTS;
DROP TABLE group_project CASCADE CONSTRAINTS;
DROP TABLE course_teach CASCADE CONSTRAINTS;
DROP TABLE assignment CASCADE CONSTRAINTS;
DROP TABLE exam CASCADE CONSTRAINTS;
DROP TABLE performs CASCADE CONSTRAINTS;
DROP TABLE task CASCADE CONSTRAINTS;

CREATE table student (
	stid number,
	password varchar2(20),
	fname varchar2(20),
	lname varchar2(20),
	major varchar2(4),
	primary key(stid));

CREATE table professor (
	profid number,
	password varchar2(20),
	fname varchar2(20),
	lname varchar2(20),
	dept varchar2(4),
	primary key(profid));

CREATE table task (
	task_id number,
	deadline date,
	descrip varchar2(30),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id));

CREATE table group_project (
	task_id number,
	stid number,
	max_size number,
	deadline date,
	descrip varchar2(30),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id, stid));

CREATE table course_teach (
	course_num number,
	course_dept varchar2(4),
	profid number,
	primary key(course_num, course_dept));

CREATE table assignment (
	task_id number,
	deadline date,
	descrip varchar2(30),
	hand_in_loc varchar2(10),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id));

CREATE table exam (
	task_id number,
	deadline date,
	descrip varchar2(30),
	cheatsheet varchar2(1),
	location varchar2(10),
	course_num number,
	course_dept varchar2(4),
	primary key(task_id));

CREATE table performs (
	stid number,
	task_id number,
	time_spent varchar2(4),
	completed varchar2(1),
	grade number,
	primary key(stid, task_id));

/*Foreign Constraints*/
ALTER TABLE group_project ADD CONSTRAINT group_task_id
	FOREIGN KEY (task_id) REFERENCES task(task_id)
	ON DELETE CASCADE;

ALTER TABLE exam ADD CONSTRAINT exam_task_id
	FOREIGN KEY (task_id) REFERENCES task(task_id)
	ON DELETE CASCADE;

ALTER TABLE assignment ADD CONSTRAINT assign_task_id
	FOREIGN KEY (task_id) REFERENCES task(task_id)
	ON DELETE CASCADE;

ALTER TABLE course_teach ADD CONSTRAINT profid
	FOREIGN KEY (profid) REFERENCES professor(profid)
	ON DELETE CASCADE;

ALTER TABLE assignment ADD CONSTRAINT assign_course
	FOREIGN KEY (course_num, course_dept) REFERENCES course_teach(course_num, course_dept)
	ON DELETE CASCADE;

ALTER TABLE exam ADD CONSTRAINT exam_course
	FOREIGN KEY (course_num, course_dept) REFERENCES course_teach(course_num, course_dept)
	ON DELETE CASCADE;

ALTER TABLE performs ADD CONSTRAINT stid
	FOREIGN KEY (stid) REFERENCES student (stid)
	ON DELETE CASCADE;

ALTER TABLE performs ADD CONSTRAINT task_id
	FOREIGN KEY (task_id) REFERENCES task(task_id)
	ON DELETE CASCADE;

