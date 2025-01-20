USE `produce_test`;

DROP TABLE IF EXISTS `produce`;

CREATE TABLE `produce`
(
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `name`             VARCHAR(255) NOT NULL,
    `type`             VARCHAR(16)  NOT NULL,
    `weight_in_grams`  INT          NOT NULL,
    PRIMARY KEY (id),
    INDEX         by_type (`type` ASC),
    INDEX         by_name (`name` ASC)
) ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;

INSERT INTO `produce_test`.`produce`
    (
        `id`,
        `name`,
        `type`,
        `weight_in_grams`
    )
    VALUES
    (
        1,
        'Apple',
        'fruit',
        1250
    ),(
        2,
        "Beetroot",
        "vegetable",
        99000
    );