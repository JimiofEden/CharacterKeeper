CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_name` VARCHAR(16),
  `password` VARCHAR(16),
  `email` VARCHAR(32),
  PRIMARY KEY (`user_id`)
);