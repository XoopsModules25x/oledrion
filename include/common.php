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

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Plugins\Models;

require_once dirname(__DIR__) . '/preloads/autoloader.php';
//require_once  dirname(__DIR__) . '/config.php';

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName); //$capsDirName

/** @var \XoopsDatabase $db */
/** @var Oledrion\Helper $helper */
/** @var Oledrion\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = Oledrion\Helper::getInstance();
$utility = new Oledrion\Utility();
//$configurator = new Oledrion\Common\Configurator();

$helper->loadLanguage('common');
$helper->loadLanguage('main');

$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = \Xmf\Module\Admin::iconUrl('', 32);

if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_DIRNAME', basename(dirname(__DIR__)));
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_IMAGES_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/');
    define($moduleDirNameUpper . '_IMAGES_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/assets/images/');
    define($moduleDirNameUpper . '_CLASS_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/class/');
    define($moduleDirNameUpper . '_PLUGINS_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/plugins/');
    define($moduleDirNameUpper . '_ADMIN_URL', constant($moduleDirNameUpper . '_URL') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/admin/');
    //    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($moduleDirNameUpper . '_DIRNAME'));
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    //    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_CACHE_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName . '/' . 'cache/');
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', $pathIcon32 . '/xoopsmicrobutton.gif');
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);

    // Define oledrion URL and PATH
    //    define('OLEDRION_URL', XOOPS_URL . '/modules/' . OLEDRION_DIRNAME . '/');
    //    define('OLEDRION_PATH', XOOPS_ROOT_PATH . '/modules/' . OLEDRION_DIRNAME . '/');

    // Set class path
    //    define('OLEDRION_CLASS_PATH', OLEDRION_PATH . 'class/');

    // Set image , js and css url
    //    define('OLEDRION_IMAGES_URL', OLEDRION_URL . 'assets/images/');
    define('OLEDRION_JS_URL', OLEDRION_URL . 'assets/js/');
    define('OLEDRION_CSS_URL', OLEDRION_URL . 'assets/css/');

    // Set admin URL and PATH
    //    define('OLEDRION_ADMIN_URL', OLEDRION_URL . 'admin/');
    //    define('OLEDRION_ADMIN_PATH', OLEDRION_PATH . 'admin' . '/');

    // Set gateways path
    define('OLEDRION_GATEWAY_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/class/Gateways' . '/');

    // Set plugin URL and PATH
    //    define('OLEDRION_PLUGINS_PATH', OLEDRION_PATH . 'plugins/');
    define('OLEDRION_PLUGINS_URL', OLEDRION_URL . 'plugins/');

    // Set text path
    define('OLEDRION_TEXT_PATH', XOOPS_UPLOAD_PATH . '/' . OLEDRION_DIRNAME . '/' . 'text/');

    // Set cache path
    //    define('OLEDRION_CACHE_PATH', XOOPS_UPLOAD_PATH . '/' . OLEDRION_DIRNAME . '/' . 'cache/');

    // Define here the place where main upload path
    //    define('OLEDRION_UPLOAD_URL', XOOPS_UPLOAD_URL . '/oledrion'); // WITHOUT Trailing slash
    //    define('OLEDRION_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/oledrion'); // WITHOUT Trailing slash

    // Define here the place where files attached to products are saved
    define('OLEDRION_ATTACHED_FILES_URL', XOOPS_UPLOAD_URL . '/oledrion/attached'); // WITHOUT Trailing slash
    define('OLEDRION_ATTACHED_FILES_PATH', XOOPS_UPLOAD_PATH . '/oledrion/attached'); // WITHOUT Trailing slash

    // Define here where pictures are saved
    define('OLEDRION_PICTURES_URL', XOOPS_UPLOAD_URL . '/oledrion/images'); // WITHOUT Trailing slash
    define('OLEDRION_PICTURES_PATH', XOOPS_UPLOAD_PATH . '/oledrion/images'); // WITHOUT Trailing slash

    // Maximum length of product's summary for pages (in characters)
    define('OLEDRION_SUMMARY_MAXLENGTH', 150);

    // Used in checkout to select a default country
    define('OLEDRION_DEFAULT_COUNTRY', 'US');

    // RSS Feed cache duration (in minutes)
    define('OLEDRION_RSS_CACHE', 3600);

    // Dimensions of the popup used to select product(s) when there are a lot of products
    define('OLEDRION_MAX_PRODUCTS_POPUP_WIDTH', 800);
    define('OLEDRION_MAX_PRODUCTS_POPUP_HEIGHT', 600);

    // Newsletter URL and PATH (the folder must be writable)
    define('OLEDRION_NEWSLETTER_URL', XOOPS_URL . '/uploads/oledrion/oledrion_newsletter.txt');
    define('OLEDRION_NEWSLETTER_PATH', XOOPS_ROOT_PATH . '/uploads/oledrion/oledrion_newsletter.txt');

    // CSV URL and path (the folder must be writable) and Separator
    define('OLEDRION_CSV_PATH', XOOPS_UPLOAD_PATH . '/oledrion/cvs');
    define('OLEDRION_CSV_URL', XOOPS_UPLOAD_URL . '/oledrion/cvs');
    define('OLEDRION_CSV_SEP', ';');

    // Gateway log's path (must be writable)
    // B.R. define('OLEDRION_GATEWAY_LOG_PATH', XOOPS_UPLOAD_PATH . '/oledrion/loggateway_oledrion.php');
    define('OLEDRION_GATEWAY_LOG_PATH', XOOPS_UPLOAD_PATH . '/oledrion/gateway_log.php');
    // B.R New: Filename of serialized confirmation email parameters
    define('OLEDRION_CONFIRMATION_EMAIL_FILENAME_SUFFIX', '_conf_email.parms');

    // B.R. New: Absolute path and filename of optional database update script
    // Must be located outside DOCUMENT_ROOT and change permissions to 'rwxr-x--x'
    define('OLEDRION_DB_UPDATE_SCRIPT', '/home/e-smith/files/ibays/rossco/license_server/update_licenseDB.php');

    // Do you want to show the list of main categories on the category page when user is on category.php (without specifying a category to see)
    define('OLEDRION_SHOW_MAIN_CATEGORIES', true);
    // Do you want to sho the list of sub categories of the current category on the category page (when viewing a specific category)
    define('OLEDRION_SHOW_SUB_CATEGORIES', true);

    // String to use to join the list of manufacturers of each product
    define('OLEDRION_STRING_TO_JOIN_MANUFACTURERS', ', ');

    // Thumbs prefix (when thumbs are automatically created)
    define('OLEDRION_THUMBS_PREFIX', 'thumb_');

    // Popup width and height (used in the product.php page to show the media.php page)
    define('OLEDRION_POPUP_MEDIA_WIDTH', 640);
    define('OLEDRION_POPUP_MEDIA_HEIGHT', 480);

    // Maximum attached files count to display on the product page
    define('OLEDRION_MAX_ATTACHMENTS', 20);

    // Define the MP3 player's dimensions (dewplayer)
    define('OLEDRION_DEWPLAYER_WIDTH', 240); // I do not recommend to go lower than 240 pixels !!!!
    define('OLEDRION_DEWPLAYER_HEIGHT', 20);

    // Place for the "duplicated" text inside the product's title
    define('OLEDRION_DUPLICATED_PLACE', 'right'); // or 'left'

    // Define the excluded tabs in the module's administration
    // '' = don't remove anything
    // To remove the first, third and fourth tabs only, type : '0,2,4'
    define('OLEDRION_EXCLUDED_TABS', '');

    // When this option is set to false, if Product A has Product B as a related product but Product A is not noted as related to Product B then the display of product A will display Product B as a related product.
    // But Product B will not show Product A as a related product.
    // When this option is set to true, Product A and Product B display each other as two related products even if Product A was not set as a related product to Product A.
    define('OLEDRION_RELATED_BOTH', true);

    // Do we resize pictures when they are smaller than defined dimensions  ?
    define('OLEDRION_DONT_RESIZE_IF_SMALLER', true);

    // Do you want to automatically fill the manual date when you create a new product ?
    define('OLEDRION_AUTO_FILL_MANUAL_DATE', true);

    // Set this option to true if you can't see the products when you add them to your cart
    define('OLEDRION_CART_BUG', false);

    // Set this option to true if your theme uses jQuery, else, set it to false
    define('OLEDRION_MY_THEME_USES_JQUERY', true);

    // Set Text file names
    define('OLEDRION_TEXTFILE1', 'oledrion_index.txt');
    define('OLEDRION_TEXTFILE2', 'oledrion_cgv.txt');
    define('OLEDRION_TEXTFILE3', 'oledrion_recomm.txt');
    define('OLEDRION_TEXTFILE4', 'oledrion_offlinepayment.txt');
    define('OLEDRION_TEXTFILE5', 'oledrion_restrictorders.txt');
    define('OLEDRION_TEXTFILE6', 'oledrion_checkout1.txt');
    define('OLEDRION_TEXTFILE7', 'oledrion_checkout2.txt');

    // Set SMS gateway
    define('OLEDRION_SMS_GATEWAY', 'example');
}

//_CACHE_PATH  XOOPS_UPLOAD_PATH . '/' . OLEDRION_DIRNAME . '/' . 'cache/'
//OLEDRION_PICTURES_URL
//OLEDRION_SUMMARY_MAXLENGTH
//OLEDRION_STRING_TO_JOIN_MANUFACTURERS

// Classes for plugins
//require_once OLEDRION_CLASS_PATH . 'Plugin.php'; // Main class
//require_once OLEDRION_PLUGINS_PATH . 'models/Action.php'; // model
//require_once OLEDRION_PLUGINS_PATH . 'models/Filter.php'; // model

// Les classes métier ou utilitaires (non ORM)
//require_once OLEDRION_CLASS_PATH . 'Utility.php';
//require_once OLEDRION_CLASS_PATH . 'HandlerManager.php';
//require_once OLEDRION_CLASS_PATH . 'Parameters.php';
//require_once OLEDRION_CLASS_PATH . 'Currency.php';
//require_once OLEDRION_CLASS_PATH . 'Shelf.php';
//require_once OLEDRION_CLASS_PATH . 'ShelfParameters.php';
//require_once OLEDRION_CLASS_PATH . 'oledrion_reductions.php';
//require_once OLEDRION_CLASS_PATH . 'Gateways.php';
//require_once OLEDRION_ADMIN_PATH . 'gateways/gateway.php'; // Abstract class
//require_once OLEDRION_CLASS_PATH . 'Lists.php';
//require_once OLEDRION_CLASS_PATH . 'Sms.php';

$oledrionHandlers = Oledrion\HandlerManager::getInstance();

$myts = \MyTextSanitizer::getInstance();

// Loading handlers
$caddyHandler           = new Oledrion\CaddyHandler($db);
$categoryHandler        = new Oledrion\CategoryHandler($db);
$commandsHandler        = new Oledrion\CommandsHandler($db);
$discountsHandler       = new Oledrion\DiscountsHandler($db);
$filesHandler           = new Oledrion\FilesHandler($db);
$gatewaysOptionsHandler = new Oledrion\GatewaysOptionsHandler($db);
$manufacturerHandler    = new Oledrion\ManufacturerHandler($db);
$persistentCartHandler  = new Oledrion\PersistentCartHandler($db);
$productsHandler        = new Oledrion\ProductsHandler($db);
$productsmanuHandler    = new Oledrion\ProductsmanuHandler($db);
$relatedHandler         = new Oledrion\RelatedHandler($db);
$vatHandler             = new Oledrion\VatHandler($db);
$vendorsHandler         = new Oledrion\VendorsHandler($db);
$votedataHandler        = new Oledrion\VotedataHandler($db);
// Added by voltan
$attributesHandler       = new Oledrion\AttributesHandler($db);
$caddyAttributesHandler  = new Oledrion\CaddyAttributesHandler($db);
$deliveryHandler         = new Oledrion\DeliveryHandler($db);
$deliveryPaymentHandler  = new Oledrion\DeliveryPaymentHandler($db);
$listsHandler            = new Oledrion\ListsHandler($db);
$locationDeliveryHandler = new Oledrion\LocationDeliveryHandler($db);
$locationHandler         = new Oledrion\LocationHandler($db);
$packingHandler          = new Oledrion\PackingHandler($db);
$paymentHandler          = new Oledrion\PaymentHandler($db);
$paymentLogHandler       = new Oledrion\PaymentLogHandler($db);
$productsListHandler     = new Oledrion\ProductsListHandler($db);

$shelf           = new Oledrion\Shelf(); // Facade
$shelfParameters = new Oledrion\ShelfParameters(); // Parameters of the facade

// Definition of Images
if (!defined('_OLEDRION_EDIT')) {
    //    global $xoopsConfig;
    //    if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/main.php')) {
    //        include OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/main.php';
    //    } else {
    //        include OLEDRION_PATH . 'language/english/main.php';
    //    }
    //    $helper->loadLanguage('main');
}

$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);


$icons = [
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
    'track'    => "<img src='" . OLEDRION_IMAGES_URL . "track.png' alt='" . _OLEDRION_TRACK . "' align='middle'>",
];

// Loading some preferences
$mod_pref = [
    //    'money_short'     => Oledrion\Utility::getModuleOption('money_short'),
    //    'money_full'      => Oledrion\Utility::getModuleOption('money_full'),
    //    'url_rewriting'   => Oledrion\Utility::getModuleOption('urlrewriting'),
    //    'tooltip'         => Oledrion\Utility::getModuleOption('infotips'),
    //    'advertisement'   => Oledrion\Utility::getModuleOption('advertisement'),
    //    'rss'             => Oledrion\Utility::getModuleOption('use_rss'),
    //    'nostock_msg'     => Oledrion\Utility::getModuleOption('nostock_msg'),
    //    'use_price'       => Oledrion\Utility::getModuleOption('use_price'),
    //    'restrict_orders' => Oledrion\Utility::getModuleOption('restrict_orders'),
    //    'isAdmin'         => Oledrion\Utility::isAdmin()


//    'money_short'     => $helper->getConfig('money_short'),
//    'money_full'      => $helper->getConfig('money_full'),
//    'url_rewriting'   => $helper->getConfig('urlrewriting'),
//    'tooltip'         => $helper->getConfig('infotips'),
//    'advertisement'   => $helper->getConfig('advertisement'),
//    'rss'             => $helper->getConfig('use_rss'),
//    'nostock_msg'     => $helper->getConfig('nostock_msg'),
//    'use_price'       => $helper->getConfig('use_price'),
//    'restrict_orders' => $helper->getConfig('restrict_orders'),
//    'isAdmin'         => $helper->isUserAdmin(),
];


//$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
//$pathModIcon32 = $helper->getModule()->getInfo('modicons32');


$icons2 = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='" . 0 . "' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='" . 1 . "' align='middle'>",
];
$debug = false;

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);
// Local icons path
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');

    $GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
}
