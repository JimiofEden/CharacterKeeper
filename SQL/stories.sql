CREATE TABLE `stories` (
  `story_id` INT AUTO_INCREMENT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT,
  `character_id` INT,
  `game_id` INT,
  `story` BLOB,
  PRIMARY KEY (`story_id`)
);