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
 */

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

// Load config file
require XOOPS_ROOT_PATH . '/modules/oledrion/config.php';

// Classes for plugins
require_once OLEDRION_CLASS_PATH . 'oledrion_plugins.php'; // Main class
require_once OLEDRION_PLUGINS_PATH . 'models/oledrion_action.php'; // model
require_once OLEDRION_PLUGINS_PATH . 'models/oledrion_filter.php'; // model

// Les classes métier ou utilitaires (non ORM)
require_once OLEDRION_CLASS_PATH . 'utility.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_handlers.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_parameters.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_currency.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_shelf.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_shelf_parameters.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_reductions.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_gateways.php';
require_once OLEDRION_ADMIN_PATH . 'gateways/gateway.php'; // Abstract class
require_once OLEDRION_CLASS_PATH . 'oledrion_lists.php';
require_once OLEDRION_CLASS_PATH . 'oledrion_sms.php';

$oledrionHandlers = OledrionHandler::getInstance();

$myts = MyTextSanitizer::getInstance();

// Loading handlers
$h_oledrion_manufacturer     = $oledrionHandlers->h_oledrion_manufacturer;
$h_oledrion_products         = $oledrionHandlers->h_oledrion_products;
$h_oledrion_productsmanu     = $oledrionHandlers->h_oledrion_productsmanu;
$h_oledrion_caddy            = $oledrionHandlers->h_oledrion_caddy;
$h_oledrion_cat              = $oledrionHandlers->h_oledrion_cat;
$h_oledrion_commands         = $oledrionHandlers->h_oledrion_commands;
$h_oledrion_related          = $oledrionHandlers->h_oledrion_related;
$h_oledrion_vat              = $oledrionHandlers->h_oledrion_vat;
$h_oledrion_votedata         = $oledrionHandlers->h_oledrion_votedata;
$h_oledrion_discounts        = $oledrionHandlers->h_oledrion_discounts;
$h_oledrion_vendors          = $oledrionHandlers->h_oledrion_vendors;
$h_oledrion_files            = $oledrionHandlers->h_oledrion_files;
$h_oledrion_persistent_cart  = $oledrionHandlers->h_oledrion_persistent_cart;
$h_oledrion_gateways_options = $oledrionHandlers->h_oledrion_gateways_options;
// Added by voltan
$h_oledrion_attributes        = $oledrionHandlers->h_oledrion_attributes;
$h_oledrion_caddy_attributes  = $oledrionHandlers->h_oledrion_caddy_attributes;
$h_oledrion_products_list     = $oledrionHandlers->h_oledrion_products_list;
$h_oledrion_lists             = $oledrionHandlers->h_oledrion_lists;
$h_oledrion_delivery          = $oledrionHandlers->h_oledrion_delivery;
$h_oledrion_location          = $oledrionHandlers->h_oledrion_location;
$h_oledrion_packing           = $oledrionHandlers->h_oledrion_packing;
$h_oledrion_payment           = $oledrionHandlers->h_oledrion_payment;
$h_oledrion_location_delivery = $oledrionHandlers->h_oledrion_location_delivery;
$h_oledrion_delivery_payment  = $oledrionHandlers->h_oledrion_delivery_payment;
$h_oledrion_payment_log       = $oledrionHandlers->h_oledrion_payment_log;

$oledrion_shelf            = new oledrion_shelf(); // Facade
$oledrion_shelf_parameters = new oledrion_shelf_parameters(); // Parameters of the facade

$moduleDirName = basename(dirname(__DIR__));
$helper        = Xmf\Module\Helper::getHelper($moduleDirName);
$helper->loadLanguage('main');

// Definition of Images
if (!defined('_OLEDRION_EDIT')) {
//    global $xoopsConfig;
//    if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/main.php')) {
//        include OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/main.php';
//    } else {
//        include OLEDRION_PATH . 'language/english/main.php';
//    }
    $helper->loadLanguage('main');
}

$pathIcon16    = Xmf\Module\Admin::iconUrl('', 16);


$icones = [
    'edit'     => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _OLEDRION_EDIT . "' align='middle'>",
    'delete'   => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _OLEDRION_DELETE . "' align='middle'>",
    'online'   => "<img src='" . OLEDRION_IMAGES_URL . "online.gif' alt='" . _OLEDRION_ONLINE . "' align='middle'>",
    'offline'  => "<img src='" . OLEDRION_IMAGES_URL . "offline.gif' alt='" . _OLEDRION_OFFLINE . "' align='middle'>",
    'ok'       => "<img src='" . OLEDRION_IMAGES_URL . "ok.png' alt='" . _OLEDRION_VALIDATE_COMMAND . "' align='middle'>",
    'copy'     => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _OLEDRION_DUPLICATE_PRODUCT . "' align='middle'>",
    'details'  => "<img src='" . OLEDRION_IMAGES_URL . "details.png' alt='" . _OLEDRION_DETAILS . "' align='middle'>",
    'print'    => "<img src='" . OLEDRION_IMAGES_URL . "print.png' alt='" . _OLEDRION_PRINT_VERSION . "' align='middle'>",
    'delivery' => "<img src='" . OLEDRION_IMAGES_URL . "delivery.png' alt='" . _OLEDRION_DELIVERY . "' align='middle'>",
    'package'  => "<img src='" . OLEDRION_IMAGES_URL . "package.png' alt='" . _OLEDRION_PACK . "' align='middle'>",
    'submit'   => "<img src='" . OLEDRION_IMAGES_URL . "submit.png' alt='" . _OLEDRION_SUBMIT . "' align='middle'>",
    'track'    => "<img src='" . OLEDRION_IMAGES_URL . "track.png' alt='" . _OLEDRION_TRACK . "' align='middle'>"
];

// Loading some preferences
$mod_pref = [
//    'money_short'     => OledrionUtility::getModuleOption('money_short'),
//    'money_full'      => OledrionUtility::getModuleOption('money_full'),
//    'url_rewriting'   => OledrionUtility::getModuleOption('urlrewriting'),
//    'tooltip'         => OledrionUtility::getModuleOption('infotips'),
//    'advertisement'   => OledrionUtility::getModuleOption('advertisement'),
//    'rss'             => OledrionUtility::getModuleOption('use_rss'),
//    'nostock_msg'     => OledrionUtility::getModuleOption('nostock_msg'),
//    'use_price'       => OledrionUtility::getModuleOption('use_price'),
//    'restrict_orders' => OledrionUtility::getModuleOption('restrict_orders'),
//    'isAdmin'         => OledrionUtility::isAdmin()
        'money_short'     => $helper->getConfig('money_short'),
        'money_full'      => $helper->getConfig('money_full'),
        'url_rewriting'   => $helper->getConfig('urlrewriting'),
        'tooltip'         => $helper->getConfig('infotips'),
        'advertisement'   => $helper->getConfig('advertisement'),
        'rss'             => $helper->getConfig('use_rss'),
        'nostock_msg'     => $helper->getConfig('nostock_msg'),
        'use_price'       => $helper->getConfig('use_price'),
        'restrict_orders' => $helper->getConfig('restrict_orders'),
        'isAdmin'         => $helper->isUserAdmin(),

];
