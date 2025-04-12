CREATE SCHEMA `WeatherOS`;

CREATE TABLE `WeatherOS`.`sht41` (
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `tempurature` FLOAT NOT NULL,
    `humidity` FLOAT NOT NULL,
    PRIMARY KEY (`timestamp`),
    UNIQUE INDEX `timestamp_UNIQUE` (`timestamp` ASC)
);

CREATE TABLE `WeatherOS`.`bmp280` (
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `temperature` FLOAT NOT NULL,
    `pressure` FLOAT NOT NULL,
    PRIMARY KEY (`timestamp`),
    UNIQUE INDEX `timestamp_UNIQUE` (`timestamp` ASC)
);

CREATE TABLE `WeatherOS`.`dsk` (
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `value` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`timestamp`),
    UNIQUE INDEX `timestamp_UNIQUE` (`timestamp` ASC)
);