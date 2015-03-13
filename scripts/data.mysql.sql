SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO `daysofweek` (`day`) VALUES
('2010-12-13'),
('2010-12-14'),
('2010-12-15'),
('2010-12-16'),
('2010-12-17'),
('2010-12-18'),
('2010-12-19');

INSERT INTO timesheets (timesheet_user, timesheet_project, timesheet_hours, timesheet_date, timesheet_descr) VALUES
(5, 1, 4, '2010-12-09', 'opierdalanie sie'),
(5, 1, 4, '2010-12-10', 'bumelowanie'),
(5, 1, 4, '2010-12-11', 'leniuchowanie'),
(5, 1, 4, '2010-12-12', 'praca'),
(5, 1, 4, '2010-12-13', 'pierdzenie w stolek'),
(5, 1, 4, '2010-12-14', 'zbijanie bakow'),
#(5, 1, 4, '2010-12-15', 'obijanie sie'),
(5, 1, 4, '2010-12-16', 'opierdalanie sie'),
(5, 1, 4, '2010-12-17', 'bumelowanie'),
(5, 1, 4, '2010-12-18', 'leniuchowanie'),
(5, 1, 4, '2010-12-19', 'praca'),
(5, 1, 4, '2010-12-20', 'pierdzenie w stolek'),
(5, 1, 4, '2010-12-21', 'zbijanie bakow'),
(5, 1, 4, '2010-12-22', 'obijanie sie');


INSERT INTO `departments` (`department_id`, `department_name`, `department_manager`) VALUES
(1, 'Dział IT', 1);

INSERT INTO `groups` (`group_id`, `group_name`, `group_project`, `group_manager`) VALUES
(1, 'Koxy', 1, 5),
(2, 'HHH', 1, NULL);


INSERT INTO `projects` (`project_id`, `project_name`, `project_start`, `project_stop`, `project_manager`, `project_department`) VALUES
(1, 'Sprzedaż ziemniaków', '2010-10-02', '2010-12-02', 4, 1),
(10, 'Debugowanie', '2010-12-16', '2010-12-24', 1, 1);

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Użytkownik'),
(2, 'Administrator'),
(3, 'Kierownik grupy'),
(4, 'Kierownik projektu'),
(5, 'Kierownik działu'),
(6, 'Pracownik kadr'),
(7, 'Pracownik księgowości'),
(8, 'Członek zarządu');


INSERT INTO `transactions` (`transaction_id`, `transaction_value`, `transaction_title`, `transaction_date`, `transaction_user`, `transaction_account`) VALUES
(1, '500.00', 'Wynagrodzenie za miesiąc Listopad', '2010-11-01 00:00:00', 1, '0'),
(2, '700.00', 'Wynagrodzenie za miesiąc Listopad', '2010-11-01 00:00:00', 2, '0');

INSERT INTO `users` (`user_id`, `user_name`, `user_surname`, `user_hour_rate`, `user_account`, `user_role`, `user_group`, `user_email`, `user_password`, `user_project`, `user_address`) VALUES
(1, 'Paweł', 'Wrzosek', '20.00', 'WP12345', NULL, NULL, 'pawelw@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c', 10, 'ul. Błotna, Nr zachlapany\r\n01-010 Błotnie'),
(2, 'Kamil', 'Zień', '10.00', 'ZK0135', NULL, NULL, 'kamilz@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c', 10, 'Warszawa, warszawska'),
(3, 'Sebastian', 'Suchanowski', '19.00', 'SS45563', NULL, 1, 'sebastians@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c', 1, ''),
(4, 'Krzysztof', 'Wódkiewicz', '25.00', 'WK1245', NULL, 1, 'krzysztofw@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c', 1, ''),
(5, 'Kamil', 'Ostaszewski', '42.00', 'OK235', NULL, 1, 'kamilo@a.pl', '1faf0c1ab4dbcdd9543c0615f04c9b2c', 1, 'Warszawa');

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(2, 2),
(5, 3),
(1, 4),
(2, 4),
(4, 4),
(1, 5),
(2, 5),
(5, 5),
(1, 6),
(2, 6),
(1, 7),
(2, 8);
SET FOREIGN_KEY_CHECKS=1;
