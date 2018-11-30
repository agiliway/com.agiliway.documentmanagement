CREATE TABLE IF NOT EXISTS civicrm_documents (
  id               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  contact_id       INT(11) UNSIGNED NOT NULL,
  title            VARCHAR(255)     NOT NULL,
  category_id      INT(11) UNSIGNED NOT NULL,
  type_id          INT(11) UNSIGNED NOT NULL,
  campaign_id      INT(11) UNSIGNED NULL DEFAULT NULL,
  file_id          INT(11) UNSIGNED NULL DEFAULT NULL,
  creator_id       INT(11) UNSIGNED NOT NULL,
  create_date      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updater_id  INT(11) UNSIGNED NOT NULL,
  last_update_date TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `contact_id` (`contact_id`),
  INDEX `category_id` (`category_id`),
  INDEX `type_id` (`type_id`),
  INDEX `campaign_id` (`campaign_id`),
  INDEX `file_id` (`file_id`),
  INDEX `creator_id` (`creator_id`),
  INDEX `last_updater_id` (`last_updater_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;