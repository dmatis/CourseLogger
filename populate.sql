DROP TABLE student CASCADE CONSTRAINTS;
DROP TABLE professor CASCADE CONSTRAINTS;
DROP TABLE group_project CASCADE CONSTRAINTS;
DROP TABLE course_teach CASCADE CONSTRAINTS;
DROP TABLE assignment CASCADE CONSTRAINTS;
DROP TABLE exam CASCADE CONSTRAINTS;
DROP TABLE performs CASCADE CONSTRAINTS;
DROP TABLE group_performs CASCADE CONSTRAINTS;
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
	course_num number NOT NULL,
	course_dept varchar2(4) NOT NULL,
	primary key(task_id));

CREATE table group_project (
	task_id number,
	group_id number,
    max_size number,
	primary key(task_id, group_id));

CREATE table course_teach (
	course_num number,
	course_dept varchar2(4),
	profid number,
	primary key(course_num, course_dept));

CREATE table assignment (
	task_id number,
	hand_in_loc varchar2(10),
	primary key(task_id));

CREATE table exam (
	task_id number,
	cheatsheet varchar2(1),
	location varchar2(10),
	primary key(task_id));

CREATE table performs (
	stid number,
	task_id number,
	time_spent number,
	completed varchar2(1),
	grade number,
	primary key(stid, task_id));
    
CREATE table group_performs (
    stid number,
    task_id number,
    group_id number,
    time_spent number,
    completed varchar2(1),
    grade number,
    primary key(stid, task_id, group_id));


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
    
ALTER TABLE task ADD CONSTRAINT task_course
	FOREIGN KEY (course_num, course_dept) REFERENCES course_teach(course_num, course_dept)
	ON DELETE CASCADE;

ALTER TABLE performs ADD CONSTRAINT stid
	FOREIGN KEY (stid) REFERENCES student (stid)
	ON DELETE CASCADE;

ALTER TABLE performs ADD CONSTRAINT task_id
	FOREIGN KEY (task_id) REFERENCES task(task_id);

ALTER TABLE group_performs ADD CONSTRAINT group_stid
    FOREIGN KEY (stid) REFERENCES student (stid)
    ON DELETE CASCADE;

ALTER TABLE group_performs ADD CONSTRAINT perform_group_id
    FOREIGN KEY (task_id, group_id) REFERENCES group_project (task_id, group_id)
    ON DELETE CASCADE;


ALTER TABLE task ADD CHECK (deadline >= DATE '2016-01-01');

    
insert into student values (19778125, 'stud1', 'Christine', 'Legge', 'CPSC');
insert into student values (35176114, 'stud2', 'Jason', 'Masih', 'CPSC');
insert into student values (94897071, 'stud3', 'Darren', 'Matis', 'CPSC');
insert into student values (12881124, 'stud4', 'Callum', 'Campbell', 'COGS');
insert into student values (56897412, 'stud5', 'Jordan', 'Deo', 'MATH');
insert into student values (56893214, 'stud7', 'Vineet', 'Lee', 'PSYC');
insert into student values (78956321, 'stud8', 'Justin', 'Thomson', 'COMM');
insert into student values (23489652, 'stud9', 'Alex', 'Woods', 'CHIN');
insert into student values (12596478, 'stud10', 'Caledonia', 'Li', 'PHYS');
insert into student values (95321478, 'stud11', 'Maggie', 'Jenni', 'ASTR');
insert into student values (56321470, 'stud12', 'Nicole', 'Miland', 'ENGL');
insert into student values (02369874, 'stud13', 'Nick', 'Coblin', 'CRWR');
insert into student values (25963575, 'stud14', 'Christina', 'Pyde', 'ARTH');
insert into student values (22254788, 'stud15', 'Juliana', 'Saxvik', 'ANTH');
insert into student values (20147965, 'stud16', 'Aaron', 'Geddes', 'SOCI');
insert into student values (45678963, 'stud17', 'Natalie', 'Jarvis', 'POLI');
insert into student values (44569752, 'stud18', 'Emily', 'Mriso', 'STAT');
insert into student values (36547890, 'stud19', 'Alison', 'del Val', 'BIOL');
insert into student values (15689752, 'stud20', 'Andrew', 'Janz', 'COMM');
insert into student values (23895647, 'stud21', 'Rupert', 'Gray', 'FREN');

insert into professor values(1, 'prof1', 'Patrice', 'Belleville', 'CPSC');
insert into professor values(2, 'prof2', 'Laks', 'Lakshmanan', 'CPSC');
insert into professor values(3, 'prof3', 'Richard', 'Balka', 'MATH');
insert into professor values(4, 'prof4', 'Jaymie', 'Matthew', 'ASTR');
insert into professor values(5, 'prof5', 'William', 'Welch', 'STAT');
insert into professor values(6, 'prof6', 'Alfred', 'Braxton', 'ANTH');
insert into professor values(7, 'prof7', 'Douglas', 'Beder', 'PHYS');
insert into professor values(8, 'prof8', 'Cedric', 'Carter', 'KINS');
insert into professor values(9, 'prof9', 'Glen', 'Dixon', 'ENGL');
insert into professor values(10, 'prof10', 'Harry', 'Edinger', 'RELI');
insert into professor values(11, 'prof11', 'David', 'Evans', 'ENGL');
insert into professor values(12, 'prof12', 'Yaniv', 'Plan', 'MATH');
insert into professor values(13, 'prof13', 'Johnathan', 'Fleming', 'PYSC');
insert into professor values(14, 'prof14', 'Brian', 'Foley', 'ARTH');
insert into professor values(15, 'prof15', 'Robert', 'Israel', 'ANTH');
insert into professor values(16, 'prof16', 'Harold', 'Laimon', 'EDUC');
insert into professor values(17, 'prof17', 'Luis', 'Olivieri', 'BOTN');
insert into professor values(18, 'prof18', 'Terence', 'Queree', 'PHAR');
insert into professor values(19, 'prof19', 'Robert', 'Rangno', 'ASTR');
insert into professor values(20, 'prof20', 'Andrew', 'Seal', 'FREN');

insert into course_teach values(304, 'CPSC', 2);
insert into course_teach values(404, 'CPSC', 2);
insert into course_teach values(121, 'CPSC', 1);
insert into course_teach values(320, 'CPSC', 1);
insert into course_teach values(313, 'CPSC', 1);
insert into course_teach values(305, 'STAT', 5);
insert into course_teach values(306, 'STAT', 5);
insert into course_teach values(443, 'STAT', 5);
insert into course_teach values(540, 'STAT', 5);
insert into course_teach values(100, 'FREN', 20);
insert into course_teach values(200, 'FREN', 20);
insert into course_teach values(300, 'FREN', 20);
insert into course_teach values(101, 'ASTR', 4);

insert into task values(1, '2016-01-15', 'Tutorial 1', 304, 'CPSC');
insert into task values(2, '2016-01-22', 'Tutorial 2', 304, 'CPSC');
insert into task values(3, '2016-01-29', 'Tutorial 3', 304, 'CPSC');
insert into task values(4, '2016-02-04', 'Midterm 1', 304, 'CPSC');
insert into task values(5, '2016-03-05', 'Midterm 2', 304, 'CPSC');
insert into task values(6, '2016-04-07', 'Term project', 404, 'CPSC');
insert into task values(7, '2016-02-15', 'Midterm', 404, 'CPSC');
insert into task values(8, '2016-01-16', 'Assignment 1', 121, 'CPSC');
insert into task values(9, '2016-02-01', 'Assignment 2', 121, 'CPSC');
insert into task values(10, '2016-01-20', 'Lab 1', 121, 'CPSC');
insert into task values(11, '2016-01-27', 'Lab 2', 121, 'CPSC');
insert into task values(12, '2016-01-20', 'Assignment 1', 320, 'CPSC');
insert into task values(13, '2016-01-20', 'Quiz 1', 320, 'CPSC');
insert into task values(14, '2016-02-20', 'Lab 1', 320, 'CPSC');
insert into task values(15, '2016-01-12', 'Assignment 1', 313, 'CPSC');
insert into task values(16, '2016-01-19', 'Assignment 2', 313, 'CPSC');
insert into task values(17, '2016-01-26', 'Assignment 3', 313, 'CPSC');
insert into task values(18, '2016-01-26', 'Midterm 1', 313, 'CPSC');
insert into task values(19, '2016-01-7', 'Lab 1', 305, 'STAT');
insert into task values(20, '2016-01-10', 'Webwork 1', 305, 'STAT');
insert into task values(21, '2016-01-14', 'Lab 2', 305, 'STAT');
insert into task values(22, '2016-01-15', 'Webwork 1', 306, 'STAT');
insert into task values(23, '2016-01-20', 'Lab quiz 1', 306, 'STAT');
insert into task values(24, '2016-02-10', 'Webwork 2', 306, 'STAT');
insert into task values(25, '2016-01-27', 'Lab quiz 2', 306, 'STAT');
insert into task values(26, '2016-04-21', 'Final Exam', 443, 'STAT');
insert into task values(27, '2016-04-08', 'Term project', 443, 'STAT');
insert into task values(28, '2016-04-25', 'Final Exam', 540, 'STAT');
insert into task values(29, '2016-04-30', 'Thesis paper', 540, 'STAT');
insert into task values(30, '2016-02-10', 'Oral presentation', 100, 'FREN');
insert into task values(31, '2016-01-30', 'Introduction paragraph', 100, 'FREN');
insert into task values(32, '2016-01-25', 'Quiz 1', 100, 'FREN');
insert into task values(33, '2016-01-15', 'Oral presentation', 200, 'FREN');
insert into task values(34, '2016-02-6', 'Reading comprehension', 200, 'FREN');
insert into task values(35, '2016-03-30', 'Group skit', 200, 'FREN');
insert into task values(36, '2016-03-30', 'Essay', 300, 'FREN');
insert into task values(37, '2016-02-15', 'Book review', 300, 'FREN');
insert into task values(38, '2016-01-15', 'Assignment 1', 101, 'ASTR');

insert into group_project values(27, 1, 2);
insert into group_project values(27, 2, 4);
insert into group_project values(6, 1, 4);
insert into group_project values(6, 2, 4);
insert into group_project values(6, 3, 4);
insert into group_project values(35, 1, 3);
insert into group_project values(30, 1, 2);
insert into group_project values(30, 2, 2);    

insert into assignment values(1, 'Box 1');
insert into assignment values(2, 'Box 1');
insert into assignment values(3, 'Box 1');
insert into exam values(4, 'N', 'DMP 310');
insert into exam values(5, 'N', 'DMP 310');
insert into exam values(7, 'N', 'DMP 110');
insert into assignment values(8, 'Box 2');
insert into assignment values(9, 'Box 2');
insert into assignment values(10, 'in lab');
insert into assignment values(11, 'in lab');
insert into assignment values(12, 'Box 3');
insert into exam values(13, 'Y', 'DMP 310');
insert into assignment values(14, 'Box 3');
insert into assignment values(15, 'handin');
insert into assignment values(16, 'handin');
insert into assignment values(17, 'handin');
insert into exam values(18, 'N', 'DMP 301');
insert into assignment values(19, 'in lab');
insert into assignment values(20, 'online');
insert into assignment values(21, 'in lab');
insert into assignment values(22, 'online');
insert into exam values(23, 'Y', 'ESB 1012');
insert into assignment values(24, 'online');
insert into exam values(25, 'Y', 'ESB 1012');
insert into exam values(26, 'Y', 'WOOD 5');
insert into exam values(28, 'N', 'ESB 2012');
insert into assignment values(29, 'in class');
insert into assignment values(31, 'email');
insert into exam values(32, 'N', 'BUCH A 101');
insert into assignment values(33, 'in class');
insert into assignment values(34, 'in class');
insert into assignment values(36, 'email');
insert into assignment values(37, 'email');
insert into assignment values(38, 'in class');

insert into performs values(19778125, 1, 1, 'Y', 90)/*completed all 304 tasks*/; 
insert into performs values(19778125, 2, 1, 'Y', 100);
insert into performs values(19778125, 3, 1, 'Y', 95);
insert into performs values(19778125, 4, 5, 'Y', 85);
insert into performs values(19778125, 5, 6, 'Y', 90);
insert into performs values(35176114, 1, 1, 'Y', 85)/*completed all 304 tasks*/; 
insert into performs values(35176114, 2, 1, 'Y', 95);
insert into performs values(35176114, 3, 1, 'Y', 100);
insert into performs values(35176114, 4, 5, 'Y', 97);
insert into performs values(35176114, 5, 6, 'Y', 70);
insert into performs values(94897071, 1, 1, 'Y', 75);
insert into performs values(94897071, 2, 1, 'N', null);
insert into performs values(94897071, 3, 1, 'Y', 67);
insert into performs values(94897071, 4, 5, 'Y', 90);
insert into performs values(94897071, 5, 6, 'Y', 81);
insert into performs values(12881124, 1, 1, 'Y', 75)/*completed all 304 tasks*/; 
insert into performs values(12881124, 2, 1, 'Y', 91);
insert into performs values(12881124, 3, 1, 'Y', 67);
insert into performs values(12881124, 4, 5, 'Y', 90);
insert into performs values(12881124, 5, 6, 'Y', 81);
insert into performs values(56897412, 1, 1, 'Y', 91);
insert into performs values(56897412, 2, 1, 'N', null);
insert into performs values(56897412, 3, 1, 'Y', 62);
insert into performs values(56897412, 4, 5, 'Y', 70);
insert into performs values(56897412, 5, 6, 'Y', 100);
insert into performs values(56893214, 7, 6, 'Y', 61);
insert into performs values(78956321, 7, 6, 'Y', 71);
insert into performs values(23489652, 7, 8, 'Y', 82);
insert into performs values(12596478, 7, 7, 'Y', 75);
insert into performs values(95321478, 8, 7, 'Y', 60);
insert into performs values(95321478, 9, 3, 'N', null);
insert into performs values(95321478, 10, 7, 'Y', 75);
insert into performs values(95321478, 11, 4, 'N', null);
insert into performs values(56321470, 8, 7, 'Y', 60);
insert into performs values(56321470, 9, 3, 'N', null);
insert into performs values(56321470, 10, 9, 'Y', 77);
insert into performs values(56321470, 11, 4, 'Y', 80);
insert into performs values(02369874, 8, 3, 'Y', 45);
insert into performs values(02369874, 9, 5, 'Y', 63);
insert into performs values(02369874, 10, 7, 'Y', 75);
insert into performs values(02369874, 11, 4, 'N', null);
insert into performs values(25963575, 8, 9, 'Y', 100);
insert into performs values(25963575, 9, 8, 'Y', 100);
insert into performs values(25963575, 10, 15, 'Y', 100);
insert into performs values(25963575, 11, 10, 'Y', 100);
insert into performs values(22254788, 8, 7, 'Y', 60)/*completed all cpsc 121 assignments*/; 
insert into performs values(22254788, 9, 9, 'Y', 91);
insert into performs values(22254788, 10, 7, 'Y', 75);
insert into performs values(22254788, 11, 10, 'Y', 85);
insert into performs values(20147965, 8, 7, 'Y', 60);
insert into performs values(20147965, 9, 3, 'N', null);
insert into performs values(20147965, 10, 7, 'Y', 75);
insert into performs values(20147965, 11, 4, 'N', null);
insert into performs values(45678963, 12, 3, 'Y', 92);
insert into performs values(45678963, 13, 7, 'Y', 75);
insert into performs values(45678963, 14, 4, 'N', null);
insert into performs values(44569752, 12, 3, 'N', null);
insert into performs values(44569752, 13, 7, 'Y', 75);
insert into performs values(44569752, 14, 4, 'Y', 100);
insert into performs values(36547890, 12, 3, 'Y', 85)/*completed all cpsc 320 assignments*/;
insert into performs values(36547890, 13, 7, 'Y', 95);
insert into performs values(36547890, 14, 4, 'Y', 93);
insert into performs values(19778125, 15, 1, 'Y', 90); 
insert into performs values(19778125, 16, 1, 'Y', 100);
insert into performs values(19778125, 17, 1, 'N', null);
insert into performs values(19778125, 18, 5, 'Y', 85);
insert into performs values(35176114, 15, 1, 'Y', 85)/*completed all cpsc 313 tasks*/; 
insert into performs values(35176114, 16, 1, 'Y', 95);
insert into performs values(35176114, 17, 1, 'Y', 100);
insert into performs values(35176114, 18, 5, 'Y', 97);
insert into performs values(94897071, 15, 1, 'Y', 75);
insert into performs values(94897071, 16, 1, 'Y', 56);
insert into performs values(94897071, 17, 1, 'N', null);
insert into performs values(94897071, 18, 5, 'Y', 90);
insert into performs values(02369874, 15, 3, 'Y', 45);
insert into performs values(02369874, 16, 5, 'Y', 63);
insert into performs values(02369874, 17, 7, 'N', null);
insert into performs values(02369874, 18, 4, 'Y', 65);
insert into performs values(45678963, 19, 5, 'Y', 63)/*completed all stat 305 tasks*/;
insert into performs values(45678963, 20, 1, 'Y', 98);
insert into performs values(45678963, 21, 8, 'Y', 65);
insert into performs values(22254788, 19, 6, 'Y', 78)/*completed all stat 305 tasks*/;
insert into performs values(22254788, 20, 4, 'Y', 100);
insert into performs values(22254788, 21, 2, 'Y', 75);
insert into performs values(15689752, 19, 1, 'Y', 78);
insert into performs values(15689752, 20, 5, 'Y', 52);
insert into performs values(15689752, 21, 9, 'N', null);
insert into performs values(23895647, 19, 5, 'Y', 68);
insert into performs values(23895647, 20, 5, 'Y', 98);
insert into performs values(23895647, 21, 3, 'N', null);
insert into performs values(45678963, 22, 5, 'Y', 63);
insert into performs values(45678963, 23, 1, 'Y', 98);
insert into performs values(45678963, 24, 8, 'N', null);
insert into performs values(45678963, 25, 3, 'Y', 65);
insert into performs values(22254788, 22, 5, 'Y', 78);
insert into performs values(22254788, 23, 9, 'Y', 65);
insert into performs values(22254788, 24, 2, 'N', null);
insert into performs values(22254788, 25, 5, 'Y', 75);
insert into performs values(15689752, 22, 7, 'Y', 78)/*completed all stat 306 tasks*/;
insert into performs values(15689752, 23, 1, 'Y', 52);
insert into performs values(15689752, 24, 4, 'Y', 64);
insert into performs values(15689752, 25, 6, 'Y', 85);
insert into performs values(23895647, 22, 8, 'Y', 68);
insert into performs values(23895647, 23, 2, 'Y', 98);
insert into performs values(23895647, 24, 5, 'N', null);
insert into performs values(23895647, 25, 3, 'Y', 74);
insert into performs values(35176114, 22, 6, 'Y', 85)/*completed all stat 306 tasks*/; 
insert into performs values(35176114, 23, 2, 'Y', 95);
insert into performs values(35176114, 24, 3, 'Y', 99);
insert into performs values(35176114, 25, 4, 'Y', 91);

insert into group_performs values(23895647, 27, 1, 2, 'Y',  72) /*group 1 of STAT 443 group project*/;
insert into group_performs values(15689752, 27, 1, 3, 'Y',  72); 
insert into group_performs values(22254788, 27, 1, 4, 'Y',  72);
insert into group_performs values(45678963, 27, 1, 5, 'Y',  72);
insert into group_performs values(12881124, 27, 2, 5, 'N',  null) /*group 2 of STAT 443 group project*/;
insert into group_performs values(02369874, 27, 2, 10, 'N',  null);
insert into group_performs values(19778125, 27, 2, 7, 'N',  null);
insert into group_performs values(45678963, 27, 2, 5, 'N',  null);
insert into group_performs values(19778125, 6, 1, 11, 'Y',  97);
insert into group_performs values(35176114, 6, 1, 11, 'Y',  97);
insert into group_performs values(94897071, 6, 1, 11, 'Y',  97);
insert into group_performs values(12881124, 6, 1, 9, 'Y',  96);
insert into group_performs values(56893214, 6, 2, 10, 'N', null);
insert into group_performs values(78956321, 6, 2, 5, 'N', null);
insert into group_performs values(23489652, 6, 2, 8, 'N', null);
insert into group_performs values(12596478, 6, 2, 9, 'N', null);
insert into group_performs values(56897412, 6, 3, 7, 'N', null);
insert into group_performs values(56893214, 6, 3, 7, 'N', null);
insert into group_performs values(78956321, 6, 3, 7, 'N', null);

 
