--Turn off Foreign Key checks for easy import
SET FOREIGN_KEY_CHECKS = 0;

--Drop the following tables if they exist
DROP TABLE IF EXISTS `rentals`;
DROP TABLE IF EXISTS `bikes`;
DROP TABLE IF EXISTS `users`;

-- Creating the users table with relevant fields
CREATE TABLE `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email`    VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating bikes table with relevant fields
CREATE TABLE `bikes` (
  `bike_id`    INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11) NOT NULL,
  `make`       VARCHAR(50)  NOT NULL,         -- Giant, Cannondale, Carrera etc..
  `model`      VARCHAR(100) NOT NULL,         -- Carrera Fury, Giant Propel Advanced 2, etc..
  `bike_type`  VARCHAR(50)  NOT NULL,         -- Mountain, Racer, Foldable etc..
  `frame_size` VARCHAR(20)  NOT NULL,         -- ML 21.5' for example. Frame sizing dependent on user height.
  `gear_count` INT(3)       NOT NULL,         
  `year`       YEAR        NOT NULL,          
  `location`   VARCHAR(100) NOT NULL,         -- Location users are able to pick the bike up from
  `rental_rate` DECIMAL(6,2) NOT NULL,        -- Cost per day of renting
  `condition`  VARCHAR(50)  DEFAULT 'Good',    -- Condition of bike according to owner
  `image_url`  VARCHAR(150) NOT NULL,         
  PRIMARY KEY (`bike_id`),
  KEY `fk_bikes_user` (`user_id`),
  CONSTRAINT `fk_bikes_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating rentals table for simple and easy booking.
CREATE TABLE `rentals` (
  `rental_id`  INT(11) NOT NULL AUTO_INCREMENT, --Rental ID to track current bike rentals
  `bike_id`    INT(11) NOT NULL,    --Bike user has chosen to rent
  `user_id`    INT(11) NOT NULL,    --User who has rented said bike
  `start_date` DATE       NOT NULL,   --Start date the user rented the bike
  `end_date`   DATE       NOT NULL,   --End date (ie. date the bike should be returned)
  `total_rate` DECIMAL(8,2) NOT NULL,    -- total rate calculated in £
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rental_id`),
  KEY `fk_rentals_bike` (`bike_id`),
  KEY `fk_rentals_user` (`user_id`),
  CONSTRAINT `fk_rentals_bike`
    FOREIGN KEY (`bike_id`)
    REFERENCES `bikes` (`bike_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_rentals_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Turn Foreign Key checks back on yp ensure consistency in data from now on
SET FOREIGN_KEY_CHECKS = 1;
