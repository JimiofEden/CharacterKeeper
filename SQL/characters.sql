CREATE TABLE `characters` (
  `char_id` INT AUTO_INCREMENT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT,
  `game_id` INT,
  `name` VARCHAR(50),
  `class` VARCHAR(16),
  `race` VARCHAR(16),
  `level` INT,
  `approved` TINYINT(1) DEFAULT 0,
  `char_sheet` VARCHAR(32),
  PRIMARY KEY (`char_id`)
);