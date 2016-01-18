CREATE TABLE `oledrion_manufacturer` (
  `manu_id` int(10) unsigned NOT NULL auto_increment,
  `manu_name` varchar(255) NOT NULL,
  `manu_commercialname` varchar(255) NOT NULL,
  `manu_email` varchar(255) NOT NULL,
  `manu_bio` text,
  `manu_url` varchar(255) NOT NULL,
  `manu_photo1` varchar(255) NOT NULL,
  `manu_photo2` varchar(255) NOT NULL,
  `manu_photo3` varchar(255) NOT NULL,
  `manu_photo4` varchar(255) NOT NULL,
  `manu_photo5` varchar(255) NOT NULL,
  PRIMARY KEY  (`manu_id`),
  KEY `manu_name` (`manu_name`),
  KEY `manu_commercialname` (`manu_commercialname`),
  KEY `manu_bio` (`manu_bio` (500))
) ENGINE=InnoDB;

CREATE TABLE `oledrion_products` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `product_cid` int(5) unsigned NOT NULL default '0',
  `product_title` varchar(255) NOT NULL default '',
  `product_vendor_id` int(10) unsigned NOT NULL,
  `product_sku` varchar(60) NOT NULL,
  `product_extraid` varchar(50) NOT NULL,
  `product_width` varchar(50) NOT NULL,
  `product_length` varchar(50) NOT NULL,
  `product_unitmeasure1` varchar(20) NOT NULL,
  `product_url` varchar(255) NOT NULL,
  `product_url2` varchar(255) NOT NULL,
  `product_url3` varchar(255) NOT NULL,
  `product_image_url` varchar(255) NOT NULL,
  `product_thumb_url` varchar(255) NOT NULL,
  `product_submitter` int(11) unsigned NOT NULL default '0',
  `product_online` tinyint(1) NOT NULL default '0',
  `product_date` varchar(255) NOT NULL,
  `product_submitted` int(10) unsigned NOT NULL,
  `product_hits` int(11) unsigned NOT NULL default '0',
  `product_rating` int(11) unsigned NOT NULL default '0',
  `product_votes` int(11) unsigned NOT NULL default '0',
  `product_comments` int(11) unsigned NOT NULL default '0',
  `product_price` decimal(16,2) NOT NULL,
  `product_shipping_price` decimal(16,2) NOT NULL,
  `product_discount_price` decimal(16,2) NOT NULL,
  `product_stock` mediumint(8) unsigned NOT NULL,
  `product_alert_stock` mediumint(8) unsigned NOT NULL,
  `product_summary` text,
  `product_description` text,
  `product_attachment` varchar(255) NOT NULL,
  `product_weight` varchar(20) NOT NULL,
  `product_unitmeasure2` varchar(20) NOT NULL,
  `product_vat_id` mediumint(8) unsigned NOT NULL,
  `product_download_url` varchar(255) NOT NULL,
  `product_recommended` date NOT NULL,
  `product_metakeywords` varchar(255) NOT NULL,
  `product_metadescription` varchar(255) NOT NULL,
  `product_metatitle` varchar(255) NOT NULL,
  `product_delivery_time` mediumint(8) unsigned NOT NULL,
  `product_ecotaxe` decimal(16,2) NOT NULL,
  `product_property1` varchar(255) NOT NULL,
  `product_property2` varchar(255) NOT NULL,
  `product_property3` varchar(255) NOT NULL,
  `product_property4` varchar(255) NOT NULL,
  `product_property5` varchar(255) NOT NULL,
  `product_property6` varchar(255) NOT NULL,
  `product_property7` varchar(255) NOT NULL,
  `product_property8` varchar(255) NOT NULL,
  `product_property9` varchar(255) NOT NULL,
  `product_property10` varchar(255) NOT NULL,
  PRIMARY KEY  (`product_id`),
  KEY `product_cid` (`product_cid`),
  KEY `product_online` (`product_online`),
  KEY `product_title` (`product_title`),
  KEY `product_unitmeasure1` (`product_unitmeasure1`),
  KEY `product_weight` (`product_weight`),
  KEY `product_vendor_id` (`product_vendor_id`),
  KEY `product_extraid` (`product_extraid`),
  KEY `product_width` (`product_width`),
  KEY `recent_online` (`product_online`,`product_submitted`),
  KEY `product_recommended` (`product_recommended`),
  KEY `product_summary` (`product_summary` (1000)),
  KEY `product_description` (`product_description` (1000))
) ENGINE=InnoDB;

CREATE TABLE `oledrion_productsmanu` (
  `pm_id` int(10) unsigned NOT NULL auto_increment,
  `pm_product_id` int(10) unsigned NOT NULL,
  `pm_manu_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`pm_id`),
  KEY `pm_product_id` (`pm_product_id`),
  KEY `pm_manu_id` (`pm_manu_id`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_caddy` (
  `caddy_id` int(10) unsigned NOT NULL auto_increment,
  `caddy_product_id` int(10) unsigned NOT NULL,
  `caddy_qte` mediumint(8) unsigned NOT NULL,
  `caddy_price` decimal(16,2) NOT NULL,
  `caddy_cmd_id` int(10) unsigned NOT NULL,
  `caddy_shipping` double(7,2) NOT NULL,
  `caddy_pass` varchar(32) NOT NULL,
  PRIMARY KEY  (`caddy_id`),
  KEY `caddy_cmd_id` (`caddy_cmd_id`),
  KEY `caddy_pass` (`caddy_pass`),
  KEY `caddy_product_id` (`caddy_product_id`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_cat` (
  `cat_cid` int(5) unsigned NOT NULL auto_increment,
  `cat_pid` int(5) unsigned NOT NULL default '0',
  `cat_title` varchar(255) NOT NULL default '',
  `cat_imgurl` varchar(255) NOT NULL default '',
  `cat_description` text,
  `cat_advertisement` text,
  `cat_metatitle` varchar(255) NOT NULL,
  `cat_metadescription` varchar(255) NOT NULL,
  `cat_metakeywords` varchar(255) NOT NULL,
  `cat_footer` text,
  PRIMARY KEY  (`cat_cid`),
  KEY `cat_pid` (`cat_pid`),
  KEY `cat_title` (`cat_title`),
  KEY `cat_description` (`cat_description` (300))
) ENGINE=InnoDB ;

CREATE TABLE `oledrion_commands` (
  `cmd_id` int(10) unsigned NOT NULL auto_increment,
  `cmd_uid` int(10) unsigned NOT NULL,
  `cmd_date` date NOT NULL,
  `cmd_create` int(10) unsigned NOT NULL,
  `cmd_state` tinyint(1) unsigned NOT NULL,
  `cmd_ip` varchar(32) NOT NULL,
  `cmd_lastname` varchar(255) NOT NULL,
  `cmd_firstname` varchar(255) NOT NULL,
  `cmd_adress` text,
  `cmd_zip` varchar(30) NOT NULL,
  `cmd_town` varchar(255) NOT NULL,
  `cmd_country` varchar(3) NOT NULL,
  `cmd_telephone` varchar(30) NOT NULL,
  `cmd_mobile` varchar(30) NOT NULL,
  `cmd_email` varchar(255) NOT NULL,
  `cmd_articles_count` mediumint(8) unsigned NOT NULL,
  `cmd_total` double(16,2) NOT NULL,
  `cmd_shipping` decimal(16,2) NOT NULL,
  `cmd_packing_price` decimal(16,2) NOT NULL,
  `cmd_bill` tinyint(1) unsigned NOT NULL default '0',
  `cmd_password` varchar(32) NOT NULL,
  `cmd_text` text,
  `cmd_cancel` varchar(32) NOT NULL,
  `cmd_comment` text,
  `cmd_vat_number` varchar(255) NOT NULL,
  `cmd_packing` varchar(255) NOT NULL,
  `cmd_packing_id` int(5) unsigned NOT NULL,
  `cmd_location` varchar(255) NOT NULL,
  `cmd_location_id` int(5) unsigned NOT NULL,
  `cmd_delivery` varchar(255) NOT NULL,
  `cmd_delivery_id` int(5) unsigned NOT NULL,
  `cmd_payment` varchar(255) NOT NULL,
  `cmd_payment_id` int(5) unsigned NOT NULL,
  `cmd_status` tinyint(1) unsigned NOT NULL default '1',
  `cmd_track` varchar(255) NOT NULL,
  `cmd_gift` varchar(255) NOT NULL,
  PRIMARY KEY  (`cmd_id`),
  KEY `cmd_date` (`cmd_date`),
  KEY `cmd_status` (`cmd_status`),
  KEY `cmd_uid` (`cmd_uid`)
) ENGINE=InnoDB ;

CREATE TABLE `oledrion_related` (
  `related_id` int(10) unsigned NOT NULL auto_increment,
  `related_product_id` int(10) unsigned NOT NULL,
  `related_product_related` int(10) unsigned NOT NULL,
  `related_product_percent` int(4) unsigned NOT NULL,
  PRIMARY KEY  (`related_id`),
  KEY `seealso` (`related_product_id`,`related_product_related`),
  KEY `related_product_id` (`related_product_id`),
  KEY `related_product_related` (`related_product_related`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_vat` (
  `vat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `vat_rate` double(5,2) NOT NULL,
  `vat_country` varchar(3) NOT NULL,
  PRIMARY KEY  (`vat_id`),
  KEY `vat_rate` (`vat_rate`, `vat_country`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_votedata` (
  `vote_ratingid` int(11) unsigned NOT NULL auto_increment,
  `vote_product_id` int(11) unsigned NOT NULL default '0',
  `vote_uid` int(11) unsigned NOT NULL default '0',
  `vote_rating` tinyint(3) NOT NULL default '1',
  `vote_ratinghostname` varchar(60) NOT NULL default '',
  `vote_ratingtimestamp` int(10) NOT NULL default '0',
  PRIMARY KEY  (`vote_ratingid`),
  KEY `vote_ratinguser` (`vote_uid`),
  KEY `vote_ratinghostname` (`vote_ratinghostname`),
  KEY `vote_product_id` (`vote_product_id`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_discounts` (
  `disc_id` int(10) unsigned NOT NULL auto_increment,
  `disc_title` varchar(255) NOT NULL,
  `disc_group` int(10) unsigned NOT NULL,
  `disc_cat_cid` int(10) unsigned NOT NULL,
  `disc_vendor_id` int(10) unsigned NOT NULL,
  `disc_product_id` int(10) unsigned NOT NULL,
  `disc_price_type` tinyint(1) unsigned NOT NULL,
  `disc_price_degress_l1qty1` mediumint(8) unsigned NOT NULL,
  `disc_price_degress_l1qty2` mediumint(8) unsigned NOT NULL,
  `disc_price_degress_l1total` decimal(16,2) NOT NULL,
  `disc_price_degress_l2qty1` mediumint(9) NOT NULL,
  `disc_price_degress_l2qty2` mediumint(9) NOT NULL,
  `disc_price_degress_l2total` decimal(16,2) NOT NULL,
  `disc_price_degress_l3qty1` mediumint(9) NOT NULL,
  `disc_price_degress_l3qty2` mediumint(9) NOT NULL,
  `disc_price_degress_l3total` decimal(16,2) NOT NULL,
  `disc_price_degress_l4qty1` mediumint(9) NOT NULL,
  `disc_price_degress_l4qty2` mediumint(9) NOT NULL,
  `disc_price_degress_l4total` decimal(16,2) NOT NULL,
  `disc_price_degress_l5qty1` mediumint(9) NOT NULL,
  `disc_price_degress_l5qty2` mediumint(9) NOT NULL,
  `disc_price_degress_l5total` decimal(16,2) NOT NULL,
  `disc_price_amount_amount` double(16,2) NOT NULL,
  `disc_price_amount_type` tinyint(1) unsigned NOT NULL,
  `disc_price_amount_on` tinyint(1) unsigned NOT NULL,
  `disc_price_case` tinyint(1) unsigned NOT NULL,
  `disc_price_case_qty_cond` tinyint(1) NOT NULL,
  `disc_price_case_qty_value` mediumint(8) NOT NULL,
  `disc_shipping_type` tinyint(1) unsigned NOT NULL,
  `disc_shipping_free_morethan` double(16,2) NOT NULL,
  `disc_shipping_reduce_amount` double(16,2) NOT NULL,
  `disc_shipping_reduce_cartamount` double(16,2) NOT NULL,
  `disc_shipping_degress_l1qty1` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l1qty2` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l1total` double(16,2) NOT NULL,
  `disc_shipping_degress_l2qty1` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l2qty2` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l2total` double(16,2) NOT NULL,
  `disc_shipping_degress_l3qty1` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l3qty2` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l3total` double(16,2) NOT NULL,
  `disc_shipping_degress_l4qty1` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l4qty2` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l4total` double(16,2) NOT NULL,
  `disc_shipping_degress_l5qty1` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l5qty2` mediumint(8) unsigned NOT NULL,
  `disc_shipping_degress_l5total` double(16,2) NOT NULL,
  `disc_date_from` int(10) unsigned NOT NULL,
  `disc_date_to` int(10) unsigned NOT NULL,
  `disc_description` text,
  PRIMARY KEY  (`disc_id`),
  KEY `disc_group` (`disc_group`),
  KEY `disc_title` (`disc_title`),
  KEY `disc_price_type` (`disc_price_type`),
  KEY `disc_price_case` (`disc_price_case`),
  KEY `disc_date` (`disc_date_from`,`disc_date_to`),
  KEY `disc_shipping_type` (`disc_shipping_type`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_vendors` (
  `vendor_id` int(10) unsigned NOT NULL auto_increment,
  `vendor_name` varchar(150) NOT NULL,
  PRIMARY KEY  (`vendor_id`),
  KEY `vendor_name` (`vendor_name`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_files` (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `file_product_id` int(10) unsigned NOT NULL,
  `file_filename` varchar(255) NOT NULL,
  `file_description` varchar(255) NOT NULL,
  `file_mimetype` varchar(255) NOT NULL,
  PRIMARY KEY  (`file_id`),
  KEY `file_product_id` (`file_product_id`),
  KEY `file_filename` (`file_filename`),
  KEY `file_description` (`file_description`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_persistent_cart` (
  `persistent_id` int(10) unsigned NOT NULL auto_increment,
  `persistent_product_id` int(10) unsigned NOT NULL,
  `persistent_uid` mediumint(8) unsigned NOT NULL,
  `persistent_date` int(10) unsigned NOT NULL,
  `persistent_qty` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`persistent_id`),
  KEY `persistent_product_id` (`persistent_product_id`),
  KEY `persistent_uid` (`persistent_uid`),
  KEY `persistent_date` (`persistent_date`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_gateways_options` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `option_gateway` varchar(50) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `option_value` text,
  PRIMARY KEY  (`option_id`),
  KEY `option_gateway` (`option_gateway`),
  KEY `option_name` (`option_name`),
  KEY `option_gateway_name` (`option_gateway`,`option_name`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_lists` (
  `list_id` int(10) unsigned NOT NULL auto_increment,
  `list_uid` mediumint(8) unsigned NOT NULL,
  `list_title` varchar(255) NOT NULL,
  `list_date` int(10) unsigned NOT NULL,
  `list_productscount` mediumint(8) unsigned NOT NULL,
  `list_views` mediumint(8) unsigned NOT NULL,
  `list_password` varchar(50) NOT NULL,
  `list_type` tinyint(3) unsigned NOT NULL,
  `list_description` text,
  PRIMARY KEY  (`list_id`),
  KEY `list_uid` (`list_uid`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_products_list` (
  `productlist_id` int(10) unsigned NOT NULL auto_increment,
  `productlist_list_id` int(10) unsigned NOT NULL,
  `productlist_product_id` int(10) unsigned NOT NULL,
  `productlist_date` date NOT NULL,
  PRIMARY KEY  (`productlist_id`),
  KEY `productlist_list_id` (`productlist_list_id`),
  KEY `productlist_product_id` (`productlist_product_id`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_attributes` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_weight` mediumint(7) unsigned default NULL,
  `attribute_title` varchar(255) default NULL,
  `attribute_name` varchar(255) NOT NULL,
  `attribute_type` tinyint(3) unsigned default NULL,
  `attribute_mandatory` tinyint(1) unsigned default NULL,
  `attribute_values` text,
  `attribute_names` text,
  `attribute_prices` text,
  `attribute_stocks` text,
  `attribute_product_id` int(11) unsigned default NULL,
  `attribute_default_value` varchar(255) default NULL,
  `attribute_option1` mediumint(7) unsigned default NULL,
  `attribute_option2` mediumint(7) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`),
  KEY `attribute_product_id` (`attribute_product_id`),
  KEY `attribute_weight` (`attribute_weight`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_caddy_attributes` (
  `ca_id` int(10) unsigned NOT NULL auto_increment,
  `ca_cmd_id` int(10) unsigned NOT NULL,
  `ca_caddy_id` int(10) unsigned NOT NULL,
  `ca_attribute_id` int(10) unsigned NOT NULL,
  `ca_attribute_values` text,
  `ca_attribute_names` text,
  `ca_attribute_prices` text,
  PRIMARY KEY  (`ca_id`),
  KEY `ca_cmd_id` (`ca_cmd_id`),
  KEY `ca_caddy_id` (`ca_caddy_id`),
  KEY `ca_attribute_id` (`ca_attribute_id`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_packing` (
  `packing_id` int(5) unsigned NOT NULL auto_increment,
  `packing_title` varchar(255) NOT NULL default '',
  `packing_width` varchar(50) NOT NULL,
  `packing_length` varchar(50) NOT NULL,
  `packing_weight` varchar(50) NOT NULL,
  `packing_image` varchar(255) NOT NULL,
  `packing_description` text,
  `packing_price` decimal(16,2) NOT NULL,
  `packing_online` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`packing_id`),
  KEY `packing_title` (`packing_title`),
  KEY `packing_online` (`packing_online`),
  KEY `packing_price` (`packing_price`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_location` (
  `location_id` int(5) unsigned NOT NULL auto_increment,
  `location_pid` int(5) unsigned NOT NULL default '0',
  `location_title` varchar(255) NOT NULL default '',
  `location_online` tinyint(1) NOT NULL default '1',
  `location_type` enum('location','parent') NOT NULL,
  PRIMARY KEY  (`location_id`),
  KEY `location_title` (`location_title`),
  KEY `location_pid` (`location_pid`),
  KEY `location_online` (`location_online`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_delivery` (
  `delivery_id` int(10) unsigned NOT NULL auto_increment,
  `delivery_title` varchar(255) NOT NULL default '',
  `delivery_description` text,
  `delivery_online` tinyint(1) NOT NULL default '1',
  `delivery_image` varchar(255) NOT NULL,
  PRIMARY KEY  (`delivery_id`),
  KEY `delivery_title` (`delivery_title`),
  KEY `delivery_online` (`delivery_online`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_payment` (
  `payment_id` int(10) unsigned NOT NULL auto_increment,
  `payment_title` varchar(255) NOT NULL default '',
  `payment_description` text,
  `payment_online` tinyint(1) NOT NULL default '1',
  `payment_type` enum('online','offline') NOT NULL,
  `payment_gateway` varchar(64) NOT NULL default '',
  `payment_image` varchar(255) NOT NULL,
  PRIMARY KEY  (`payment_id`),
  KEY `payment_title` (`payment_title`),
  KEY `payment_online` (`payment_online`),
  KEY `payment_type` (`payment_type`),
  KEY `payment_gateway` (`payment_gateway`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_location_delivery` (
  `ld_id` int(5) unsigned NOT NULL auto_increment,
  `ld_location` int(5) unsigned NOT NULL,
  `ld_delivery` int(5) unsigned NOT NULL,
  `ld_price` decimal(16,2) NOT NULL,
  `ld_delivery_time` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`ld_id`),
  KEY `ld_location` (`ld_location`),
  KEY `ld_delivery` (`ld_delivery`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_delivery_payment` (
  `dp_id` int(5) unsigned NOT NULL auto_increment,
  `dp_delivery` int(5) unsigned NOT NULL,
  `dp_payment` int(5) unsigned NOT NULL,
  PRIMARY KEY  (`dp_id`),
  KEY `dp_delivery` (`dp_delivery`),
  KEY `dp_payment` (`dp_payment`)
) ENGINE=InnoDB;

CREATE TABLE `oledrion_payment_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `log_create` int(10) unsigned NOT NULL,
  `log_status` tinyint(1) unsigned NOT NULL,
  `log_ip` varchar(32) NOT NULL,
  `log_type` enum('online','offline') NOT NULL,
  `log_payment` int(10) unsigned NOT NULL,
  `log_gateway` varchar(64) NOT NULL default '',
  `log_uid` int(10) unsigned NOT NULL,
  `log_command` int(10) unsigned NOT NULL,
  `log_amount` double(16,2) NOT NULL,
  `log_authority` varchar(255) NOT NULL,
  PRIMARY KEY  (`log_id`),
  KEY `log_uid` (`log_uid`),
  KEY `log_command` (`log_command`),
  KEY `log_status` (`log_status`)
) ENGINE=InnoDB;