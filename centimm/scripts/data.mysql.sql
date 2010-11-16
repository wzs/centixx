SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `departments` (`department_id`, `department_name`, `department_manager`) VALUES
(1, 'Dział IT', 1);

INSERT INTO `groups` (`group_id`, `group_name`, `group_project`, `group_manager`) VALUES
(1, 'Workgroup', 1, 1),
(2, 'Murzyni', 1, 3);

INSERT INTO `projects` (`project_id`, `project_name`, `project_start`, `project_stop`, `project_manager`, `project_department`) VALUES
(1, 'Centimm', '2010-11-14', '2010-12-17', 5, 1);

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'group_manager'),
(4, 'project_manager'),
(5, 'department_chief'),
(6, 'hr'),
(7, 'accountant'),
(8, 'ceo');



INSERT INTO `users` (`user_id`, `user_name`, `user_surname`, `user_hour_rate`, `user_account`, `user_role`, `user_group`, `user_email`, `user_password`) VALUES
(1, 'Paweł', 'Wrzosek', '30', '12345', 1, 1, 'pawel@a.pl', '47bce5c74f589f4867dbd57e9ca9f808'),
(2, 'Kamil', 'Zień', '30', '12346', 1, 2, 'Kamil@a.pl', '47bce5c74f589f4867dbd57e9ca9f808'),
(3, 'Sebastian', 'Suchanowski', '30', '12347', 1, 1, 'Sebastian@a.pl', '47bce5c74f589f4867dbd57e9ca9f808'),
(4, 'Krzysztof', 'Wódkiewicz', '30', '12348', 1, 1, 'Krzysztof@a.pl', '47bce5c74f589f4867dbd57e9ca9f808'),
(5, 'Kamil', 'Ostaszewski', '30', '12349', 1, 1, 'Kamil@a.pl', '47bce5c74f589f4867dbd57e9ca9f808');
SET FOREIGN_KEY_CHECKS=1;

