-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles` (
  `role_id` INT NOT NULL AUTOINCREMENT ,
  `role_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`role_id`) );


-- -----------------------------------------------------
-- Table `departments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `departments` (
  `department_id` INT NOT NULL AUTOINCREMENT ,
  `department_name` VARCHAR(45) NOT NULL ,
  `department_manager` INT NULL ,
  PRIMARY KEY (`department_id`) );


-- -----------------------------------------------------
-- Table `projects`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `projects` (
  `project_id` INT NOT NULL AUTOINCREMENT ,
  `project_name` VARCHAR(45) NOT NULL ,
  `project_start` DATE NOT NULL ,
  `project_stop` DATE NOT NULL ,
  `project_manager` INT NOT NULL ,
  `project_department` INT NOT NULL ,
  PRIMARY KEY (`project_id`) );


-- -----------------------------------------------------
-- Table `groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `groups` (
  `group_id` INT NOT NULL AUTOINCREMENT ,
  `group_name` VARCHAR(45) NOT NULL ,
  `group_project` INT NOT NULL ,
  `group_manager` INT NOT NULL ,
  PRIMARY KEY (`group_id`) );


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTOINCREMENT ,
  `user_name` VARCHAR(45) NOT NULL ,
  `user_surname` VARCHAR(45) NOT NULL ,
  `user_hour_rate` DECIMAL(10,0)  NOT NULL ,
  `user_account` DECIMAL(10,0)  NOT NULL ,
  `user_role` INT NOT NULL ,
  `user_group` INT NULL ,
  PRIMARY KEY (`user_id`) );


-- -----------------------------------------------------
-- Table `transactions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `transactions` (
  `transaction_id` INT NOT NULL AUTOINCREMENT ,
  `transaction_account` DECIMAL(10,0)  NOT NULL ,
  `transaction_value` DECIMAL(10,0)  NOT NULL ,
  `transaction_title` VARCHAR(45) NOT NULL ,
  `transaction_date` DATETIME NOT NULL ,
  PRIMARY KEY (`transaction_id`) );


-- -----------------------------------------------------
-- Table `timesheets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `timesheets` (
  `users_user_id` INT NOT NULL ,
  `projects_project_id` INT NOT NULL ,
  `timesheet_hours` DECIMAL(10,0)  NOT NULL ,
  `timesheet_date` DATE NOT NULL ,
  PRIMARY KEY (`users_user_id`, `projects_project_id`) );
