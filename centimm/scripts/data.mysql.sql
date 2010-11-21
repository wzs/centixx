SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES 'utf8';

INSERT INTO `departments` (`department_id`, `department_name`, `department_manager`) VALUES
(1, 'Dział IT', 1);

INSERT INTO `groups` (`group_id`, `group_name`, `group_project`, `group_manager`) VALUES
(1, 'Workgroup', 1, 1),
(2, 'Ludzie od czarnej roboty', 1, 3);

INSERT INTO `projects` (`project_id`, `project_name`, `project_start`, `project_stop`, `project_manager`, `project_department`) VALUES
(1, 'Centimm', '2010-11-14', '2010-12-17', 5, 1);

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Użytkownik'),
(2, 'Administrator'),
(3, 'Kierownik grupy'),
(4, 'Kierownik projektu'),
(5, 'Kierownik działu'),
(6, 'Pracownik kadr'),
(7, 'Pracownik księgowości'),
(8, 'Członek zarządu');

INSERT INTO `users` (`user_id`, `user_name`, `user_surname`, `user_hour_rate`, `user_account`, `user_role`, `user_group`, `user_email`, `user_password`) VALUES
(1, 'Paweł', 'Wrzosek', '30', '12345', 5, 2, 'pawelw@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c'),
(2, 'Kamil', 'Zień', '30', '12346', 1, 2, 'kamilz@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c'),
(3, 'Sebastian', 'Suchanowski', '30', '12347', 1, 2, 'sebastians@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c'),
(4, 'Krzysztóf', 'Wódkiewicz', '30', '12348', 1, 2, 'krzysztofw@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c'),
(5, 'Kamil', 'Ostaszewski', '30', '12349', 6, 1, 'kamilo@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c');

SET FOREIGN_KEY_CHECKS=1;

