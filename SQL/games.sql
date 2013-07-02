CREATE TABLE `games` (
  `game_id` INT AUTO_INCREMENT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT,
  `game_name` VARCHAR(32),
  `game_setting` VARCHAR(60),
  `game_description` BLOB,
  PRIMARY KEY (`game_id`)
);