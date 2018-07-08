<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * oledrion
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 * @param $module
 * @param $version
 */

use XoopsModules\Oledrion;

/**
 * @param $module
 * @param $version
 */
function xoops_module_update_oledrion($module, $version)
{
    global $xoopsDB;

    // Présence des nouvelles tables et nouvelles zones dans la base de données
    // Nouvelle table oledrion_gateways_options
    $tableName = $xoopsDB->prefix('oledrion_gateways_options');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = 'CREATE TABLE ' . $tableName . " (
                `option_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `option_gateway` VARCHAR(50) NOT NULL COMMENT 'nom de la passerelle de paiement',
                `option_name` VARCHAR(50) NOT NULL,
                `option_value` TEXT NOT NULL,
                PRIMARY KEY  (`option_id`),
                KEY `option_gateway` (`option_gateway`),
                KEY `option_name` (`option_name`),
                KEY `option_gateway_name` (`option_gateway`,`option_name`)
                ) ENGINE=InnoDB";
        $xoopsDB->queryF($sql);
    }

    // Nouveau champ cmd_comment dans Commands
    $tableName = $xoopsDB->prefix('oledrion_commands');
    if (!Oledrion\Utility::fieldExists('cmd_comment', $tableName)) {
        Oledrion\Utility::addField('`cmd_comment` TEXT NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_vat_number', $tableName)) {
        Oledrion\Utility::addField('`cmd_vat_number` VARCHAR( 255 ) NOT NULL', $tableName);
    }

    /**
     * Nouvelle table oledrion_lists
     * @since 2.2.2009.01.29
     */
    $tableName = $xoopsDB->prefix('oledrion_lists');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = 'CREATE TABLE ' . $tableName . ' (
                `list_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `list_uid` MEDIUMINT(8) UNSIGNED NOT NULL,
                `list_title` VARCHAR(255) NOT NULL,
                `list_date` INT(10) UNSIGNED NOT NULL,
                `list_productscount` MEDIUMINT(8) UNSIGNED NOT NULL,
                `list_views` MEDIUMINT(8) UNSIGNED NOT NULL,
                `list_password` VARCHAR(50) NOT NULL,
                `list_type` TINYINT(3) UNSIGNED NOT NULL,
                `list_description` TEXT NOT NULL,
                PRIMARY KEY  (`list_id`),
                KEY `list_uid` (`list_uid`)
                ) ENGINE=InnoDB';
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_lists
     * @since 2.2.2009.01.29
     */
    $tableName = $xoopsDB->prefix('oledrion_products_list');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = 'CREATE TABLE ' . $tableName . ' (
                `productlist_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `productlist_list_id` INT(10) UNSIGNED NOT NULL,
                `productlist_product_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY  (`productlist_id`),
                KEY `productlist_list_id` (`productlist_list_id`),
                KEY `productlist_product_id` (`productlist_product_id`)
                ) ENGINE=InnoDB';
        $xoopsDB->queryF($sql);
    }

    if (!Oledrion\Utility::fieldExists('productlist_date', $tableName)) {
        Oledrion\Utility::addField('productlist_date DATE NOT NULL', $tableName);
    }

    /**
     * Nouvelle table oledrion_attributes
     * @since 2.3.2009.03.09
     */
    $tableName = $xoopsDB->prefix('oledrion_attributes');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
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
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_caddy_attributes
     * @since 2.3.2009.03.10
     */
    $tableName = $xoopsDB->prefix('oledrion_caddy_attributes');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
              `ca_id` int(10) unsigned NOT NULL auto_increment,
              `ca_cmd_id` int(10) unsigned NOT NULL,
              `ca_caddy_id` int(10) unsigned NOT NULL,
              `ca_attribute_id` int(10) unsigned NOT NULL,
              `ca_attribute_values` text NOT NULL,
              `ca_attribute_names` text NOT NULL,
              `ca_attribute_prices` text NOT NULL,
              PRIMARY KEY  (`ca_id`),
              KEY `ca_cmd_id` (`ca_cmd_id`),
              KEY `ca_caddy_id` (`ca_caddy_id`),
              KEY `ca_attribute_id` (`ca_attribute_id`)
        ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Augmentation des types numéraires pour accepter le million
     * @since 2.3.2009.04.20
     */
    $definition = Oledrion\Utility::getFieldDefinition('product_price', $xoopsDB->prefix('oledrion_products'));
    if ('' !== $definition) {
        if ('decimal(7,2)' === xoops_trim($definition['Type'])) {
            $tablesToUpdates = [
                'oledrion_products'  => [
                    'product_price',
                    'product_shipping_price',
                    'product_discount_price',
                    'product_ecotaxe',
                ],
                'oledrion_caddy'     => ['caddy_price'],
                'oledrion_commands'  => ['cmd_shipping'],
                'oledrion_discounts' => [
                    'disc_price_degress_l1total',
                    'disc_price_degress_l2total',
                    'disc_price_degress_l3total',
                    'disc_price_degress_l4total',
                    'disc_price_degress_l5total',
                ],
            ];
            foreach ($tablesToUpdates as $tableName => $fields) {
                foreach ($fields as $field) {
                    $sql = 'ALTER TABLE ' . $xoopsDB->prefix($tableName) . ' CHANGE `' . $field . '` `' . $field . '` DECIMAL( 16, 2 ) NOT NULL';
                    $xoopsDB->queryF($sql);
                }
            }
        }
    }

    /**
     * Add product_property
     * @since 2.3.2012.08.03
     */
    $tableName = $xoopsDB->prefix('oledrion_products');
    if (!Oledrion\Utility::fieldExists('product_property1', $tableName)) {
        Oledrion\Utility::addField('`product_property1` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property2', $tableName)) {
        Oledrion\Utility::addField('`product_property2` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property3', $tableName)) {
        Oledrion\Utility::addField('`product_property3` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property4', $tableName)) {
        Oledrion\Utility::addField('`product_property4` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property5', $tableName)) {
        Oledrion\Utility::addField('`product_property5` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property6', $tableName)) {
        Oledrion\Utility::addField('`product_property6` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property7', $tableName)) {
        Oledrion\Utility::addField('`product_property7` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property8', $tableName)) {
        Oledrion\Utility::addField('`product_property8` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property9', $tableName)) {
        Oledrion\Utility::addField('`product_property9` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_property10', $tableName)) {
        Oledrion\Utility::addField('`product_property10` varchar(255) NOT NULL', $tableName);
    }

    /**
     * Nouvelle table oledrion_packing
     * @since 2.3.4 2013.03.5
     */
    $tableName = $xoopsDB->prefix('oledrion_packing');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
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
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_location
     * @since 2.3.4 2013.03.5
     */
    $tableName = $xoopsDB->prefix('oledrion_location');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
              `location_id` int(5) unsigned NOT NULL auto_increment,
              `location_pid` int(5) unsigned NOT NULL default '0',
              `location_title` varchar(255) NOT NULL default '',
              `location_online` tinyint(1) NOT NULL default '1',
              `location_type` enum('location','parent') NOT NULL,
              PRIMARY KEY  (`location_id`),
              KEY `location_title` (`location_title`),
              KEY `location_pid` (`location_pid`),
              KEY `location_online` (`location_online`)
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_delivery
     * @since 2.3.4 2013.03.5
     */
    $tableName = $xoopsDB->prefix('oledrion_delivery');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
              `delivery_id` int(10) unsigned NOT NULL auto_increment,
              `delivery_title` varchar(255) NOT NULL default '',
              `delivery_description` text,
              `delivery_online` tinyint(1) NOT NULL default '1',
              `delivery_image` varchar(255) NOT NULL,
              PRIMARY KEY  (`delivery_id`),
              KEY `delivery_title` (`delivery_title`),
              KEY `delivery_online` (`delivery_online`)
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_payment
     * @since 2.3.4 2013.03.5
     */
    $tableName = $xoopsDB->prefix('oledrion_payment');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
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
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_location_delivery
     * @since 2.3.4 2013.03.5
     */
    $tableName = $xoopsDB->prefix('oledrion_location_delivery');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
              `ld_id` int(5) unsigned NOT NULL auto_increment,
              `ld_location` int(5) unsigned NOT NULL,
              `ld_delivery` int(5) unsigned NOT NULL,
              `ld_price` decimal(16,2) NOT NULL,
              `ld_delivery_time` mediumint(8) unsigned NOT NULL,
              PRIMARY KEY  (`ld_id`),
              KEY `ld_location` (`ld_location`),
              KEY `ld_delivery` (`ld_delivery`)
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_delivery_payment
     * @since 2.3.4 2013.03.5
     */

    $tableName = $xoopsDB->prefix('oledrion_delivery_payment');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
              `dp_id` int(5) unsigned NOT NULL auto_increment,
              `dp_delivery` int(5) unsigned NOT NULL,
              `dp_payment` int(5) unsigned NOT NULL,
              PRIMARY KEY  (`dp_id`),
              KEY `dp_delivery` (`dp_delivery`),
              KEY `dp_payment` (`dp_payment`)
            ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Nouvelle table oledrion_delivery_payment
     * @since 2.3.4 2013.03.15
     */

    $tableName = $xoopsDB->prefix('oledrion_payment_log');
    if (!Oledrion\Utility::tableExists($tableName)) {
        $sql = "CREATE TABLE `$tableName` (
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
                ) ENGINE=InnoDB;";
        $xoopsDB->queryF($sql);
    }

    /**
     * Add New fields to Commands
     * @since 2.3.2013.03.15
     */
    $tableName = $xoopsDB->prefix('oledrion_commands');
    if (!Oledrion\Utility::fieldExists('cmd_create', $tableName)) {
        Oledrion\Utility::addField('`cmd_create` int(10) unsigned NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_packing', $tableName)) {
        Oledrion\Utility::addField('`cmd_packing` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_packing_id', $tableName)) {
        Oledrion\Utility::addField('`cmd_packing_id` int(5) unsigned NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_location', $tableName)) {
        Oledrion\Utility::addField('`cmd_location` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_location_id', $tableName)) {
        Oledrion\Utility::addField('`cmd_location_id` int(5) unsigned NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_delivery', $tableName)) {
        Oledrion\Utility::addField('`cmd_delivery` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_delivery_id', $tableName)) {
        Oledrion\Utility::addField('`cmd_delivery_id` int(5) unsigned NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_payment', $tableName)) {
        Oledrion\Utility::addField('`cmd_payment` varchar(255) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_payment_id', $tableName)) {
        Oledrion\Utility::addField('`cmd_payment_id` int(5) unsigned NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_status', $tableName)) {
        Oledrion\Utility::addField('`cmd_status` tinyint(1) unsigned NOT NULL default "1"', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_mobile', $tableName)) {
        Oledrion\Utility::addField('`cmd_mobile` varchar(30) NOT NULL', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('cmd_packing_price', $tableName)) {
        Oledrion\Utility::addField('`cmd_packing_price` decimal(16,2) NOT NULL', $tableName);
    }

    /**
     * Add/update product urls
     * @since 2.3.2013.08.03
     */
    $tableName = $xoopsDB->prefix('oledrion_products');

    if (!Oledrion\Utility::fieldExists('product_url2', $tableName)) {
        Oledrion\Utility::addField('`product_url2` VARCHAR( 255 ) NOT NULL AFTER `product_url`', $tableName);
    }

    if (!Oledrion\Utility::fieldExists('product_url3', $tableName)) {
        Oledrion\Utility::addField('`product_url3` VARCHAR( 255 ) NOT NULL AFTER `product_url`', $tableName);
    }

    /**
     * Add cmd_track
     * @since 2014.01.03
     */
    $tableName = $xoopsDB->prefix('oledrion_commands');

    if (!Oledrion\Utility::fieldExists('cmd_track', $tableName)) {
        Oledrion\Utility::addField('`cmd_track` VARCHAR( 255 ) NOT NULL', $tableName);
    }

    /**
     * Add cmd_track
     * @since 2014.01.10
     */
    $tableName = $xoopsDB->prefix('oledrion_related');

    if (!Oledrion\Utility::fieldExists('related_product_percent', $tableName)) {
        Oledrion\Utility::addField('`related_product_percent` INT( 4 ) NOT NULL', $tableName);
    }
}
