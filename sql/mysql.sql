CREATE TABLE `oledrion_manufacturer` (
  `manu_id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manu_name`           VARCHAR(255)     NOT NULL,
  `manu_commercialname` VARCHAR(255)     NOT NULL,
  `manu_email`          VARCHAR(255)     NOT NULL,
  `manu_bio`            TEXT,
  `manu_url`            VARCHAR(255)     NOT NULL,
  `manu_photo1`         VARCHAR(255)     NOT NULL,
  `manu_photo2`         VARCHAR(255)     NOT NULL,
  `manu_photo3`         VARCHAR(255)     NOT NULL,
  `manu_photo4`         VARCHAR(255)     NOT NULL,
  `manu_photo5`         VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`manu_id`),
  KEY `manu_name` (`manu_name`),
  KEY `manu_commercialname` (`manu_commercialname`),
  KEY `manu_bio` (`manu_bio` (500))
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_products` (
  `product_id`              INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `product_cid`             INT(5) UNSIGNED       NOT NULL DEFAULT '0',
  `product_title`           VARCHAR(255)          NOT NULL DEFAULT '',
  `product_vendor_id`       INT(10) UNSIGNED      NOT NULL,
  `product_sku`             VARCHAR(60)           NOT NULL,
  `product_extraid`         VARCHAR(50)           NOT NULL,
  `product_width`           VARCHAR(50)           NOT NULL,
  `product_length`          VARCHAR(50)           NOT NULL,
  `product_unitmeasure1`    VARCHAR(20)           NOT NULL,
  `product_url`             VARCHAR(255)          NOT NULL,
  `product_url2`            VARCHAR(255)          NOT NULL,
  `product_url3`            VARCHAR(255)          NOT NULL,
  `product_image_url`       VARCHAR(255)          NOT NULL,
  `product_thumb_url`       VARCHAR(255)          NOT NULL,
  `product_submitter`       INT(11) UNSIGNED      NOT NULL DEFAULT '0',
  `product_online`          TINYINT(1)            NOT NULL DEFAULT '0',
  `product_date`            VARCHAR(255)          NOT NULL,
  `product_submitted`       INT(10) UNSIGNED      NOT NULL,
  `product_hits`            INT(11) UNSIGNED      NOT NULL DEFAULT '0',
  `product_rating`          INT(11) UNSIGNED      NOT NULL DEFAULT '0',
  `product_votes`           INT(11) UNSIGNED      NOT NULL DEFAULT '0',
  `product_comments`        INT(11) UNSIGNED      NOT NULL DEFAULT '0',
  `product_price`           DECIMAL(16, 2)        NOT NULL,
  `product_shipping_price`  DECIMAL(16, 2)        NOT NULL,
  `product_discount_price`  DECIMAL(16, 2)        NOT NULL,
  `product_stock`           MEDIUMINT(8) UNSIGNED NOT NULL,
  `product_alert_stock`     MEDIUMINT(8) UNSIGNED NOT NULL,
  `product_summary`         TEXT,
  `product_description`     TEXT,
  `product_attachment`      VARCHAR(255)          NOT NULL,
  `product_weight`          VARCHAR(20)           NOT NULL,
  `product_unitmeasure2`    VARCHAR(20)           NOT NULL,
  `product_vat_id`          MEDIUMINT(8) UNSIGNED NOT NULL,
  `product_download_url`    VARCHAR(255)          NOT NULL,
  `product_recommended`     DATE                  NOT NULL,
  `product_metakeywords`    VARCHAR(255)          NOT NULL,
  `product_metadescription` VARCHAR(255)          NOT NULL,
  `product_metatitle`       VARCHAR(255)          NOT NULL,
  `product_delivery_time`   MEDIUMINT(8) UNSIGNED NOT NULL,
  `product_ecotaxe`         DECIMAL(16, 2)        NOT NULL,
  `product_property1`       VARCHAR(255)          NOT NULL,
  `product_property2`       VARCHAR(255)          NOT NULL,
  `product_property3`       VARCHAR(255)          NOT NULL,
  `product_property4`       VARCHAR(255)          NOT NULL,
  `product_property5`       VARCHAR(255)          NOT NULL,
  `product_property6`       VARCHAR(255)          NOT NULL,
  `product_property7`       VARCHAR(255)          NOT NULL,
  `product_property8`       VARCHAR(255)          NOT NULL,
  `product_property9`       VARCHAR(255)          NOT NULL,
  `product_property10`      VARCHAR(255)          NOT NULL,
  `skip_packing`            TINYINT(1)            NOT NULL DEFAULT '0',
  `skip_location`           TINYINT(1)            NOT NULL DEFAULT '0',
  `skip_delivery`           TINYINT(1)            NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `product_cid` (`product_cid`),
  KEY `product_online` (`product_online`),
  KEY `product_title` (`product_title`),
  KEY `product_unitmeasure1` (`product_unitmeasure1`),
  KEY `product_weight` (`product_weight`),
  KEY `product_vendor_id` (`product_vendor_id`),
  KEY `product_extraid` (`product_extraid`),
  KEY `product_width` (`product_width`),
  KEY `recent_online` (`product_online`, `product_submitted`),
  KEY `product_recommended` (`product_recommended`),
  KEY `product_summary` (`product_summary` (1000)),
  KEY `product_description` (`product_description` (1000))
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_productsmanu` (
  `pm_id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_product_id` INT(10) UNSIGNED NOT NULL,
  `pm_manu_id`    INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`pm_id`),
  KEY `pm_product_id` (`pm_product_id`),
  KEY `pm_manu_id` (`pm_manu_id`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_caddy` (
  `caddy_id`         INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `caddy_product_id` INT(10) UNSIGNED      NOT NULL,
  `caddy_qte`        MEDIUMINT(8) UNSIGNED NOT NULL,
  `caddy_price`      DECIMAL(16, 2)        NOT NULL,
  `caddy_cmd_id`     INT(10) UNSIGNED      NOT NULL,
  `caddy_shipping`   DOUBLE(7, 2)          NOT NULL,
  `caddy_pass`       VARCHAR(32)           NOT NULL,
  PRIMARY KEY (`caddy_id`),
  KEY `caddy_cmd_id` (`caddy_cmd_id`),
  KEY `caddy_pass` (`caddy_pass`),
  KEY `caddy_product_id` (`caddy_product_id`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_cat` (
  `cat_cid`             INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_pid`             INT(5) UNSIGNED NOT NULL DEFAULT '0',
  `cat_title`           VARCHAR(255)    NOT NULL DEFAULT '',
  `cat_imgurl`          VARCHAR(255)    NOT NULL DEFAULT '',
  `cat_description`     TEXT,
  `cat_advertisement`   TEXT,
  `cat_metatitle`       VARCHAR(255)    NOT NULL,
  `cat_metadescription` VARCHAR(255)    NOT NULL,
  `cat_metakeywords`    VARCHAR(255)    NOT NULL,
  `cat_footer`          TEXT,
  PRIMARY KEY (`cat_cid`),
  KEY `cat_pid` (`cat_pid`),
  KEY `cat_title` (`cat_title`),
  KEY `cat_description` (`cat_description` (300))
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_commands` (
  `cmd_id`             INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `cmd_uid`            INT(10) UNSIGNED      NOT NULL,
  `cmd_date`           DATE                  NOT NULL,
  `cmd_create`         INT(10) UNSIGNED      NOT NULL,
  `cmd_state`          TINYINT(1) UNSIGNED   NOT NULL,
  `cmd_ip`             VARCHAR(32)           NOT NULL,
  `cmd_lastname`       VARCHAR(255)          NOT NULL,
  `cmd_firstname`      VARCHAR(255)          NOT NULL,
  `cmd_adress`         TEXT,
  `cmd_zip`            VARCHAR(30)           NOT NULL,
  `cmd_town`           VARCHAR(255)          NOT NULL,
  `cmd_country`        VARCHAR(3)            NOT NULL,
  `cmd_telephone`      VARCHAR(30)           NOT NULL,
  `cmd_mobile`         VARCHAR(30)           NOT NULL,
  `cmd_email`          VARCHAR(255)          NOT NULL,
  `cmd_articles_count` MEDIUMINT(8) UNSIGNED NOT NULL,
  `cmd_total`          DOUBLE(16, 2)         NOT NULL,
  `cmd_shipping`       DECIMAL(16, 2)        NOT NULL,
  `cmd_packing_price`  DECIMAL(16, 2)        NOT NULL,
  `cmd_bill`           TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
  `cmd_password`       VARCHAR(32)           NOT NULL,
  `cmd_text`           TEXT,
  `cmd_cancel`         VARCHAR(32)           NOT NULL,
  `cmd_comment`        TEXT,
  `cmd_vat_number`     VARCHAR(255)          NOT NULL,
  `cmd_packing`        VARCHAR(255)          NOT NULL,
  `cmd_packing_id`     INT(5) UNSIGNED       NOT NULL,
  `cmd_location`       VARCHAR(255)          NOT NULL,
  `cmd_location_id`    INT(5) UNSIGNED       NOT NULL,
  `cmd_delivery`       VARCHAR(255)          NOT NULL,
  `cmd_delivery_id`    INT(5) UNSIGNED       NOT NULL,
  `cmd_payment`        VARCHAR(255)          NOT NULL,
  `cmd_payment_id`     INT(5) UNSIGNED       NOT NULL,
  `cmd_status`         TINYINT(1) UNSIGNED   NOT NULL DEFAULT '1',
  `cmd_track`          VARCHAR(255)          NOT NULL,
  `cmd_gift`           VARCHAR(255)          NOT NULL,
  PRIMARY KEY (`cmd_id`),
  KEY `cmd_date` (`cmd_date`),
  KEY `cmd_status` (`cmd_status`),
  KEY `cmd_uid` (`cmd_uid`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_related` (
  `related_id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `related_product_id`      INT(10) UNSIGNED NOT NULL,
  `related_product_related` INT(10) UNSIGNED NOT NULL,
  `related_product_percent` INT(4) UNSIGNED  NOT NULL,
  PRIMARY KEY (`related_id`),
  KEY `seealso` (`related_product_id`, `related_product_related`),
  KEY `related_product_id` (`related_product_id`),
  KEY `related_product_related` (`related_product_related`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_vat` (
  `vat_id`      MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vat_rate`    DOUBLE(5, 2)          NOT NULL,
  `vat_country` VARCHAR(3)            NOT NULL,
  PRIMARY KEY (`vat_id`),
  KEY `vat_rate` (`vat_rate`, `vat_country`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_votedata` (
  `vote_ratingid`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vote_product_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `vote_uid`             INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `vote_rating`          TINYINT(3)       NOT NULL DEFAULT '1',
  `vote_ratinghostname`  VARCHAR(60)      NOT NULL DEFAULT '',
  `vote_ratingtimestamp` INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_ratingid`),
  KEY `vote_ratinguser` (`vote_uid`),
  KEY `vote_ratinghostname` (`vote_ratinghostname`),
  KEY `vote_product_id` (`vote_product_id`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_discounts` (
  `disc_id`                         INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `disc_title`                      VARCHAR(255)          NOT NULL,
  `disc_group`                      INT(10) UNSIGNED      NOT NULL,
  `disc_cat_cid`                    INT(10) UNSIGNED      NOT NULL,
  `disc_vendor_id`                  INT(10) UNSIGNED      NOT NULL,
  `disc_product_id`                 INT(10) UNSIGNED      NOT NULL,
  `disc_price_type`                 TINYINT(1) UNSIGNED   NOT NULL,
  `disc_price_degress_l1qty1`       MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_price_degress_l1qty2`       MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_price_degress_l1total`      DECIMAL(16, 2)        NOT NULL,
  `disc_price_degress_l2qty1`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l2qty2`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l2total`      DECIMAL(16, 2)        NOT NULL,
  `disc_price_degress_l3qty1`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l3qty2`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l3total`      DECIMAL(16, 2)        NOT NULL,
  `disc_price_degress_l4qty1`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l4qty2`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l4total`      DECIMAL(16, 2)        NOT NULL,
  `disc_price_degress_l5qty1`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l5qty2`       MEDIUMINT(9)          NOT NULL,
  `disc_price_degress_l5total`      DECIMAL(16, 2)        NOT NULL,
  `disc_price_amount_amount`        DOUBLE(16, 2)         NOT NULL,
  `disc_price_amount_type`          TINYINT(1) UNSIGNED   NOT NULL,
  `disc_price_amount_on`            TINYINT(1) UNSIGNED   NOT NULL,
  `disc_price_case`                 TINYINT(1) UNSIGNED   NOT NULL,
  `disc_price_case_qty_cond`        TINYINT(1)            NOT NULL,
  `disc_price_case_qty_value`       MEDIUMINT(8)          NOT NULL,
  `disc_shipping_type`              TINYINT(1) UNSIGNED   NOT NULL,
  `disc_shipping_free_morethan`     DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_reduce_amount`     DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_reduce_cartamount` DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_degress_l1qty1`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l1qty2`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l1total`   DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_degress_l2qty1`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l2qty2`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l2total`   DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_degress_l3qty1`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l3qty2`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l3total`   DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_degress_l4qty1`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l4qty2`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l4total`   DOUBLE(16, 2)         NOT NULL,
  `disc_shipping_degress_l5qty1`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l5qty2`    MEDIUMINT(8) UNSIGNED NOT NULL,
  `disc_shipping_degress_l5total`   DOUBLE(16, 2)         NOT NULL,
  `disc_date_from`                  INT(10) UNSIGNED      NOT NULL,
  `disc_date_to`                    INT(10) UNSIGNED      NOT NULL,
  `disc_description`                TEXT,
  PRIMARY KEY (`disc_id`),
  KEY `disc_group` (`disc_group`),
  KEY `disc_title` (`disc_title`),
  KEY `disc_price_type` (`disc_price_type`),
  KEY `disc_price_case` (`disc_price_case`),
  KEY `disc_date` (`disc_date_from`, `disc_date_to`),
  KEY `disc_shipping_type` (`disc_shipping_type`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_vendors` (
  `vendor_id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vendor_name` VARCHAR(150)     NOT NULL,
  PRIMARY KEY (`vendor_id`),
  KEY `vendor_name` (`vendor_name`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_files` (
  `file_id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_product_id`  INT(10) UNSIGNED NOT NULL,
  `file_filename`    VARCHAR(255)     NOT NULL,
  `file_description` VARCHAR(255)     NOT NULL,
  `file_mimetype`    VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `file_product_id` (`file_product_id`),
  KEY `file_filename` (`file_filename`),
  KEY `file_description` (`file_description`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_persistent_cart` (
  `persistent_id`         INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `persistent_product_id` INT(10) UNSIGNED      NOT NULL,
  `persistent_uid`        MEDIUMINT(8) UNSIGNED NOT NULL,
  `persistent_date`       INT(10) UNSIGNED      NOT NULL,
  `persistent_qty`        MEDIUMINT(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`persistent_id`),
  KEY `persistent_product_id` (`persistent_product_id`),
  KEY `persistent_uid` (`persistent_uid`),
  KEY `persistent_date` (`persistent_date`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_gateways_options` (
  `option_id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `option_gateway` VARCHAR(50)      NOT NULL,
  `option_name`    VARCHAR(50)      NOT NULL,
  `option_value`   TEXT,
  PRIMARY KEY (`option_id`),
  KEY `option_gateway` (`option_gateway`),
  KEY `option_name` (`option_name`),
  KEY `option_gateway_name` (`option_gateway`, `option_name`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_lists` (
  `list_id`            INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `list_uid`           MEDIUMINT(8) UNSIGNED NOT NULL,
  `list_title`         VARCHAR(255)          NOT NULL,
  `list_date`          INT(10) UNSIGNED      NOT NULL,
  `list_productscount` MEDIUMINT(8) UNSIGNED NOT NULL,
  `list_views`         MEDIUMINT(8) UNSIGNED NOT NULL,
  `list_password`      VARCHAR(50)           NOT NULL,
  `list_type`          TINYINT(3) UNSIGNED   NOT NULL,
  `list_description`   TEXT,
  PRIMARY KEY (`list_id`),
  KEY `list_uid` (`list_uid`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_products_list` (
  `productlist_id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `productlist_list_id`    INT(10) UNSIGNED NOT NULL,
  `productlist_product_id` INT(10) UNSIGNED NOT NULL,
  `productlist_date`       DATE             NOT NULL,
  PRIMARY KEY (`productlist_id`),
  KEY `productlist_list_id` (`productlist_list_id`),
  KEY `productlist_product_id` (`productlist_product_id`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_attributes` (
  `attribute_id`            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `attribute_weight`        MEDIUMINT(7) UNSIGNED     DEFAULT NULL,
  `attribute_title`         VARCHAR(255)              DEFAULT NULL,
  `attribute_name`          VARCHAR(255)     NOT NULL,
  `attribute_type`          TINYINT(3) UNSIGNED       DEFAULT NULL,
  `attribute_mandatory`     TINYINT(1) UNSIGNED       DEFAULT NULL,
  `attribute_values`        TEXT,
  `attribute_names`         TEXT,
  `attribute_prices`        TEXT,
  `attribute_stocks`        TEXT,
  `attribute_product_id`    INT(11) UNSIGNED          DEFAULT NULL,
  `attribute_default_value` VARCHAR(255)              DEFAULT NULL,
  `attribute_option1`       MEDIUMINT(7) UNSIGNED     DEFAULT NULL,
  `attribute_option2`       MEDIUMINT(7) UNSIGNED     DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `attribute_product_id` (`attribute_product_id`),
  KEY `attribute_weight` (`attribute_weight`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_caddy_attributes` (
  `ca_id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ca_cmd_id`           INT(10) UNSIGNED NOT NULL,
  `ca_caddy_id`         INT(10) UNSIGNED NOT NULL,
  `ca_attribute_id`     INT(10) UNSIGNED NOT NULL,
  `ca_attribute_values` TEXT,
  `ca_attribute_names`  TEXT,
  `ca_attribute_prices` TEXT,
  PRIMARY KEY (`ca_id`),
  KEY `ca_cmd_id` (`ca_cmd_id`),
  KEY `ca_caddy_id` (`ca_caddy_id`),
  KEY `ca_attribute_id` (`ca_attribute_id`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_packing` (
  `packing_id`          INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `packing_title`       VARCHAR(255)    NOT NULL DEFAULT '',
  `packing_width`       VARCHAR(50)     NOT NULL,
  `packing_length`      VARCHAR(50)     NOT NULL,
  `packing_weight`      VARCHAR(50)     NOT NULL,
  `packing_image`       VARCHAR(255)    NOT NULL,
  `packing_description` TEXT,
  `packing_price`       DECIMAL(16, 2)  NOT NULL,
  `packing_online`      TINYINT(1)      NOT NULL DEFAULT '1',
  PRIMARY KEY (`packing_id`),
  KEY `packing_title` (`packing_title`),
  KEY `packing_online` (`packing_online`),
  KEY `packing_price` (`packing_price`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_location` (
  `location_id`     INT(5) UNSIGNED             NOT NULL AUTO_INCREMENT,
  `location_pid`    INT(5) UNSIGNED             NOT NULL DEFAULT '0',
  `location_title`  VARCHAR(255)                NOT NULL DEFAULT '',
  `location_online` TINYINT(1)                  NOT NULL DEFAULT '1',
  `location_type`   ENUM ('location', 'parent') NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_title` (`location_title`),
  KEY `location_pid` (`location_pid`),
  KEY `location_online` (`location_online`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_delivery` (
  `delivery_id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `delivery_description` TEXT,
  `delivery_online`      TINYINT(1)       NOT NULL DEFAULT '1',
  `delivery_image`       VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`delivery_id`),
  KEY `delivery_title` (`delivery_title`),
  KEY `delivery_online` (`delivery_online`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_payment` (
  `payment_id`          INT(10) UNSIGNED           NOT NULL AUTO_INCREMENT,
  `payment_title`       VARCHAR(255)               NOT NULL DEFAULT '',
  `payment_description` TEXT,
  `payment_online`      TINYINT(1)                 NOT NULL DEFAULT '1',
  `payment_type`        ENUM ('online', 'offline') NOT NULL,
  `payment_gateway`     VARCHAR(64)                NOT NULL DEFAULT '',
  `payment_image`       VARCHAR(255)               NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `payment_title` (`payment_title`),
  KEY `payment_online` (`payment_online`),
  KEY `payment_type` (`payment_type`),
  KEY `payment_gateway` (`payment_gateway`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_location_delivery` (
  `ld_id`            INT(5) UNSIGNED       NOT NULL AUTO_INCREMENT,
  `ld_location`      INT(5) UNSIGNED       NOT NULL,
  `ld_delivery`      INT(5) UNSIGNED       NOT NULL,
  `ld_price`         DECIMAL(16, 2)        NOT NULL,
  `ld_delivery_time` MEDIUMINT(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`ld_id`),
  KEY `ld_location` (`ld_location`),
  KEY `ld_delivery` (`ld_delivery`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_delivery_payment` (
  `dp_id`       INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dp_delivery` INT(5) UNSIGNED NOT NULL,
  `dp_payment`  INT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`dp_id`),
  KEY `dp_delivery` (`dp_delivery`),
  KEY `dp_payment` (`dp_payment`)
)
  ENGINE = InnoDB;

CREATE TABLE `oledrion_payment_log` (
  `log_id`        INT(10) UNSIGNED           NOT NULL AUTO_INCREMENT,
  `log_create`    INT(10) UNSIGNED           NOT NULL,
  `log_status`    TINYINT(1) UNSIGNED        NOT NULL,
  `log_ip`        VARCHAR(32)                NOT NULL,
  `log_type`      ENUM ('online', 'offline') NOT NULL,
  `log_payment`   INT(10) UNSIGNED           NOT NULL,
  `log_gateway`   VARCHAR(64)                NOT NULL DEFAULT '',
  `log_uid`       INT(10) UNSIGNED           NOT NULL,
  `log_command`   INT(10) UNSIGNED           NOT NULL,
  `log_amount`    DOUBLE(16, 2)              NOT NULL,
  `log_authority` VARCHAR(255)               NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_uid` (`log_uid`),
  KEY `log_command` (`log_command`),
  KEY `log_status` (`log_status`)
)
  ENGINE = InnoDB;
