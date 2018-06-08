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

/**
 * Various parameters for the module
 */

$moduleDirName = basename(__DIR__);

// Location of attached files (url and physical path on your disk)
if (!defined('OLEDRION_ATTACHED_FILES_URL')) {

    // Define oledrion dirname
    define('OLEDRION_DIRNAME', $moduleDirName);

    // Define oledrion URL and PATH
    define('OLEDRION_URL', XOOPS_URL . '/modules/' . OLEDRION_DIRNAME . '/');
    define('OLEDRION_PATH', XOOPS_ROOT_PATH . '/modules/' . OLEDRION_DIRNAME . '/');

    // Set class path
    define('OLEDRION_CLASS_PATH', OLEDRION_PATH . 'class/');

    // Set image , js and css url
    define('OLEDRION_IMAGES_URL', OLEDRION_URL . 'assets/images/');
    define('OLEDRION_JS_URL', OLEDRION_URL . 'assets/js/');
    define('OLEDRION_CSS_URL', OLEDRION_URL . 'assets/css/');

    // Set admin URL and PATH
    define('OLEDRION_ADMIN_URL', OLEDRION_URL . 'admin/');
    define('OLEDRION_ADMIN_PATH', OLEDRION_PATH . 'admin' . '/');

    // Set gateways path
    define('OLEDRION_GATEWAY_PATH', OLEDRION_ADMIN_PATH . 'gateways' . '/');

    // Set plugin URL and PATH
    define('OLEDRION_PLUGINS_PATH', OLEDRION_PATH . 'class/plugins/');
    define('OLEDRION_PLUGINS_URL', OLEDRION_URL . 'class/plugins/');

    // Set text path
    define('OLEDRION_TEXT_PATH', XOOPS_UPLOAD_PATH . '/' . OLEDRION_DIRNAME . '/' . 'text/');

    // Set cache path
    define('OLEDRION_CACHE_PATH', XOOPS_UPLOAD_PATH . '/' . OLEDRION_DIRNAME . '/' . 'cache/');

    // Define here the place where main upload path
    define('OLEDRION_UPLOAD_URL', XOOPS_UPLOAD_URL . '/oledrion'); // WITHOUT Trailing slash
    define('OLEDRION_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/oledrion'); // WITHOUT Trailing slash

    // Define here the place where files attached to products are saved
    define('OLEDRION_ATTACHED_FILES_URL', XOOPS_UPLOAD_URL . '/oledrion/attached'); // WITHOUT Trailing slash
    define('OLEDRION_ATTACHED_FILES_PATH', XOOPS_UPLOAD_PATH . '/oledrion/attached'); // WITHOUT Trailing slash

    // Define here where pictures are saved
    define('OLEDRION_PICTURES_URL', XOOPS_UPLOAD_URL . '/oledrion/images'); // WITHOUT Trailing slash
    define('OLEDRION_PICTURES_PATH', XOOPS_UPLOAD_PATH . '/oledrion/images'); // WITHOUT Trailing slash

    // Define here where pictures are saved
    define('OLEDRION_THUMBS_URL', XOOPS_UPLOAD_URL . '/oledrion/thumbs'); // WITHOUT Trailing slash
    define('OLEDRION_THUMBS_PATH', XOOPS_UPLOAD_PATH . '/oledrion/thumbs'); // WITHOUT Trailing slash

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
    define('OLEDRION_GATEWAY_LOG_PATH', XOOPS_UPLOAD_PATH . '/oledrion/loggateway_oledrion.php');

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
