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

// The name of this module
define('_MI_OLEDRION_NAME', 'My Shop');

// A brief description of this module
define('_MI_OLEDRION_DESC', 'Creates an online shop to display and sell products.');

// Names of blocks for this module (Not all module has blocks)
define('_MI_OLEDRION_BNAME1', 'Recent Products');
define('_MI_OLEDRION_BNAME2', 'Top Products');
define('_MI_OLEDRION_BNAME3', 'Categories');
define('_MI_OLEDRION_BNAME4', 'Best Sellers');
define('_MI_OLEDRION_BNAME5', 'Best Rated Products');
define('_MI_OLEDRION_BNAME6', 'Random Product');
define('_MI_OLEDRION_BNAME7', 'Products on promotion');
define('_MI_OLEDRION_BNAME8', 'Shopping cart');
define('_MI_OLEDRION_BNAME9', 'Recommended products');
define('_MI_OLEDRION_BNAME10', 'Recently Sold');
define('_MI_OLEDRION_BNAME11', 'Last lists');
define('_MI_OLEDRION_BNAME12', 'My lists');
define('_MI_OLEDRION_BNAME13', 'Lists of the current category');
define('_MI_OLEDRION_BNAME14', 'Random lists');
define('_MI_OLEDRION_BNAME15', 'Most viewed lists');
define('_MI_OLEDRION_BNAME16', 'Ajax search');

define('_MI_OLEDRION_BNAME1_DESC', 'Shows recently added products titles');
define('_MI_OLEDRION_BNAME2_DESC', 'Shows most viewed products titles');
define('_MI_OLEDRION_BNAME3_DESC', 'Show categories in relation with the category page');
define('_MI_OLEDRION_BNAME4_DESC', 'Show most sold products');
define('_MI_OLEDRION_BNAME5_DESC', 'Shows best rated product');
define('_MI_OLEDRION_BNAME6_DESC', 'Shows a random product');
define('_MI_OLEDRION_BNAME7_DESC', 'Shows products in promotion');
define('_MI_OLEDRION_BNAME8_DESC', 'Shows cart');
define('_MI_OLEDRION_BNAME9_DESC', 'Shows last recommended products');
define('_MI_OLEDRION_BNAME10_DESC', 'Shows Recently Sold products');
define('_MI_OLEDRION_BNAME11_DESC', 'Shows recent public lists');
define('_MI_OLEDRION_BNAME12_DESC', 'Shows user lists');
define('_MI_OLEDRION_BNAME13_DESC', 'Shows recent public lists according to the current category');
define('_MI_OLEDRION_BNAME14_DESC', 'Shows random lists');
define('_MI_OLEDRION_BNAME15_DESC', 'Shows most viewed lists');
define('_MI_OLEDRION_BNAME16_DESC', 'Shows ajax search form');

// Sub menu titles
define('_MI_OLEDRION_SMNAME1', 'Shopping cart');
define('_MI_OLEDRION_SMNAME2', 'Index');
define('_MI_OLEDRION_SMNAME3', 'Categories');
define('_MI_OLEDRION_SMNAME4', 'Categories map');
define('_MI_OLEDRION_SMNAME5', 'Whos who');
define('_MI_OLEDRION_SMNAME6', 'All products');
define('_MI_OLEDRION_SMNAME7', 'Search');
define('_MI_OLEDRION_SMNAME8', 'General Conditions Of Sale');
define('_MI_OLEDRION_SMNAME9', 'Recommended Products');
define('_MI_OLEDRION_SMNAME10', 'My lists');
define('_MI_OLEDRION_SMNAME11', 'All lists');

// Names of admin menu items
define('_MI_OLEDRION_ADMENU0', 'Vendors');
define('_MI_OLEDRION_ADMENU1', 'VAT');
define('_MI_OLEDRION_ADMENU2', 'Categories');
define('_MI_OLEDRION_ADMENU3', 'Companies');
define('_MI_OLEDRION_ADMENU4', 'Products');
define('_MI_OLEDRION_ADMENU5', 'Orders');
define('_MI_OLEDRION_ADMENU6', 'Discounts');
define('_MI_OLEDRION_ADMENU7', 'Newsletter');
define('_MI_OLEDRION_ADMENU8', 'Texts');
define('_MI_OLEDRION_ADMENU9', 'Inventory');
define('_MI_OLEDRION_ADMENU10', 'Home');
define('_MI_OLEDRION_ADMENU11', 'Files');
define('_MI_OLEDRION_ADMENU12', 'Gateways');
define('_MI_OLEDRION_ADMENU13', 'Options');
define('_MI_OLEDRION_ADMENU13_DESC', 'Product Options');
define('_MI_OLEDRION_ADMENU14', 'Blocks');
define('_MI_OLEDRION_ADMENU15', 'Lists');
define('_MI_OLEDRION_ADMENU16', 'Maintain');
define('_MI_OLEDRION_ADMENU17', 'Properties');
define('_MI_OLEDRION_ADMENU18', 'Packing');
define('_MI_OLEDRION_ADMENU19', 'Location');
define('_MI_OLEDRION_ADMENU20', 'Delivery');
define('_MI_OLEDRION_ADMENU21', 'Payment');

// Settings
define('_MI_OLEDRION_SETTING_1', 'USD');
define('_MI_OLEDRION_SETTING_2', '$');
define('_MI_OLEDRION_SETTING_3', '2');
define('_MI_OLEDRION_SETTING_4', '1');
define('_MI_OLEDRION_SETTING_5', '[space]');
define('_MI_OLEDRION_SETTING_6', ',');

// Title of config items
define('_MI_OLEDRION_NEWLINKS', 'Select the maximum number of new products displayed on top page');
define('_MI_OLEDRION_PERPAGE', 'Select the maximum number of products displayed in each page');
define('_MI_OLEDRION_RELATEDLIMIT', 'Select the maximum number of related products displayed in product page');

// Text for notifications
define('_MI_OLEDRION_GLOBAL_NOTIFY', 'Global');
define('_MI_OLEDRION_GLOBAL_NOTIFYDSC', 'Global lists notification options.');

define('_MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFY', 'New Category');
define('_MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notify me when a new product category is created.');
define('_MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Receive notification when a new product category is created.');
define('_MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify: New Product category');

define('_MI_OLEDRION_GLOBAL_NEWLINK_NOTIFY', 'New Product');
define('_MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYCAP', 'Notify me when a new product is added.');
define('_MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYDSC', 'Receive notification when a new product is added.');
define('_MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New Product');

define('_MI_OLEDRION_FORM_OPTIONS', 'Form Option');
define('_MI_OLEDRION_FORM_OPTIONS_DESC', 'Select the editor to use. If you have a simple install (e.g you use only xoops core editor class, provided in the standard XOOPS core package), then you can just select DHTML and Compact');

define('_MI_OLEDRION_FORM_COMPACT', 'Compact');
define('_MI_OLEDRION_FORM_DHTML', 'DHTML');
define('_MI_OLEDRION_FORM_SPAW', 'Spaw Editor');
define('_MI_OLEDRION_FORM_HTMLAREA', 'HtmlArea Editor');
define('_MI_OLEDRION_FORM_FCK', 'FCK Editor');
define('_MI_OLEDRION_FORM_KOIVI', 'Koivi Editor');
define('_MI_OLEDRION_FORM_TINYEDITOR', 'TinyEditor');

define('_MI_OLEDRION_INFOTIPS', 'Length of tooltips');
define('_MI_OLEDRION_INFOTIPS_DES', 'If you use this option, links related to products will contains the first (n) characters of the product. If you set this value to 0 then the infotips will be empty');
define('_MI_OLEDRION_UPLOADFILESIZE', 'MAX Filesize Upload (KB) 1048576 = 1 Meg');

define('_MI_PRODUCTSBYTHISMANUFACTURER', 'Products by the same manufacturer');

define('_MI_OLEDRION_PREVNEX_LINK', 'Show Previous and Next link?');
define('_MI_OLEDRION_PREVNEX_LINK_DESC', 'When this option is set to Yes, two new links are visible at the bottom of each product. Those links are used to go to the previous and next product according to the publish date');

define('_MI_OLEDRION_SUMMARY1_SHOW', 'Show recent products in all categories?');
define('_MI_OLEDRION_SUMMARY1_SHOW_DESC', 'When you use this option, a summary containing links to all the recent published products is visible at the bottom of each product');

define('_MI_OLEDRION_SUMMARY2_SHOW', 'Show recent products in current category ?');
define('_MI_OLEDRION_SUMMARY2_SHOW_DESC', 'When you use this option, a summary containing links to all the recent published products is visible at the bottom of each product');

define('_MI_OLEDRION_OPT23', '[METAGEN] - Maximum count of keywords to generate');
define('_MI_OLEDRION_OPT23_DSC', 'Select the maximum count of keywords to generate automatically.');

define('_MI_OLEDRION_OPT24', '[METAGEN] - Keywords order');
define('_MI_OLEDRION_OPT241', 'Create them in the order they appear in the text');
define('_MI_OLEDRION_OPT242', 'Order of words frequency');
define('_MI_OLEDRION_OPT243', 'Reverse order of words frequency');

define('_MI_OLEDRION_OPT25', '[METAGEN] - Blacklist');
define('_MI_OLEDRION_OPT25_DSC', 'Enter words (separated by a comma) to remove from meta keywords');
define('_MI_OLEDRION_RATE', 'Enable users to rate Products?');

define('_MI_OLEDRION_ADVERTISEMENT', 'Advertisement');
define('_MI_OLEDRION_ADV_DESCR', 'Enter a text or a javascript code to display in your products');
define('_MI_OLEDRION_MIMETYPES', 'Enter authorised Mime Types for upload (separated them on a new line)');
define('_MI_OLEDRION_STOCK_EMAIL', 'Email address to use when stocks are low');
define('_MI_OLEDRION_STOCK_EMAIL_DSC', 'Dont type anything if you dont want to use this function.');

define('_MI_OLEDRION_OPT7', 'Use RSS feeds ?');
define('_MI_OLEDRION_OPT7_DSC', 'The last Products will be available via an RSS Feed');

define('_MI_OLEDRION_CHUNK1', 'Span for most recent Products');
define('_MI_OLEDRION_CHUNK2', 'Span for most purchased Products');
define('_MI_OLEDRION_CHUNK3', 'Span for most viewed Products');
define('_MI_OLEDRION_CHUNK4', 'Span for best ranked Products');
define('_MI_OLEDRION_ITEMSCNT', 'Items count to display in the administration');
define('_MI_OLEDRION_PDF_CATALOG', 'Allow the use of the PDF catalog ?');
define('_MI_OLEDRION_URL_REWR', 'Use URL Rewriting ?');

define('_MI_OLEDRION_MONEY_F', 'Name of currency');
define('_MI_OLEDRION_MONEY_S', 'Symbol for currency');
define('_MI_OLEDRION_NO_MORE', 'Display products even when there is no stock available ?');
define('_MI_OLEDRION_MSG_NOMORE', 'Text to display when theres no more stock for a product');
define('_MI_OLEDRION_GRP_SOLD', 'Group to send an email when a product is sold ?');
define('_MI_OLEDRION_GRP_QTY', 'Group of users authorized to modify products quantities from the Product page');
define('_MI_OLEDRION_BEST_TOGETHER', 'Display Better Together?');
define('_MI_OLEDRION_UNPUBLISHED', 'Display product whose publication date is later than today?');
define('_MI_OLEDRION_DECIMAL', 'Decimal point for money');
define('_MI_OLEDRION_CONF04', 'Thousands separator');
define('_MI_OLEDRION_CONF05', 'Decimals separator');
define('_MI_OLEDRION_CONF00', 'Moneys position ?');
define('_MI_OLEDRION_CONF00_DSC', 'Yes = right, No = left');
define('_MI_OLEDRION_MANUAL_META', 'Enter meta data manually?');

define('_MI_OLEDRION_OFFLINE_PAYMENT', 'Do you want to enable Offline payment?');
define('_MI_OLEDRION_OFF_PAY_DSC', 'If you enable it, you must type some texts in the module Administration in the Texts tab');

define('_MI_OLEDRION_USE_PRICE', 'Do you want to use the price field?');
define('_MI_OLEDRION_USE_PRICE_DSC', 'With this option you can disable products price (to create a catalog, for example)');

define('_MI_OLEDRION_PERSISTENT_CART', 'Do you want to use the persistent cart?');
define('_MI_OLEDRION_PERSISTENT_CART_DSC', 'When this option is set to Yes, the users cart is saved (Warning: this option will consume resources)');

define('_MI_OLEDRION_RESTRICT_ORDERS', 'Restrict orders to registered users ?');
define('_MI_OLEDRION_RESTRICT_ORDERS_DSC', 'If you set this option to Yes then only the registered users can order products');

define('_MI_OLEDRION_RESIZE_MAIN', 'Do you want to automatically resize the main picture of each products picture?');
define('_MI_OLEDRION_RESIZE_MAIN_DSC', '');

define('_MI_OLEDRION_CREATE_THUMBS', 'Do you want the module to automatically create the product thumbnail?');
define('_MI_OLEDRION_CREATE_THUMBS_DSC', 'If you dont use this option then you will have to upload products thumbnails yourself');

define('_MI_OLEDRION_IMAGES_WIDTH', 'Max Images width');
define('_MI_OLEDRION_IMAGES_HEIGHT', 'Max Images height');

define('_MI_OLEDRION_THUMBS_WIDTH', 'Max Thumbnail width');
define('_MI_OLEDRION_THUMBS_HEIGHT', 'Max Thumbnail height');

define('_MI_OLEDRION_RESIZE_CATEGORIES', 'Do you also want to resize categories pictures and manufacturers pictures to the above dimensions?');
define('_MI_OLEDRION_SHIPPING_QUANTITY', 'Multiply the product shipping amount by the product quantity?');

define('_MI_OLEDRION_USE_TAGS', 'Do you want to use the tags system ? (the XOOPS TAG module must be installed)');
define('_MI_OLEDRION_TAG_CLOUD', 'Module Tag Cloud');
define('_MI_OLEDRION_TOP_TAGS', 'Module Top Tags');

define('_MI_OLEDRION_ASK_VAT_NUMBER', 'Do you want to ask your clients their Sales Tax number?');
define('_MI_OLEDRION_USE_STOCK_ATTRIBUTES', 'Do you want to manage the stocks in the products attributes?');

define('_MI_OLEDRION_COLUMNS_INDEX', 'Columns count in the module index page');
define('_MI_OLEDRION_COLUMNS_CATEGORY', 'Columns count in the category page');
define('_MI_OLEDRION_ADAPTED_LIST', 'Maximum products count to display before to replace the list with an adapted list');

define('_MI_OLEDRION_PRODUCT_PROPERTY1', 'Product Property 1');
define('_MI_OLEDRION_PRODUCT_PROPERTY2', 'Product Property 2');
define('_MI_OLEDRION_PRODUCT_PROPERTY3', 'Product Property 3');
define('_MI_OLEDRION_PRODUCT_PROPERTY4', 'Product Property 4');
define('_MI_OLEDRION_PRODUCT_PROPERTY5', 'Product Property 5');
define('_MI_OLEDRION_PRODUCT_PROPERTY6', 'Product Property 6');
define('_MI_OLEDRION_PRODUCT_PROPERTY7', 'Product Property 7');
define('_MI_OLEDRION_PRODUCT_PROPERTY8', 'Product Property 8');
define('_MI_OLEDRION_PRODUCT_PROPERTY9', 'Product Property 9');
define('_MI_OLEDRION_PRODUCT_PROPERTY10', 'Product Property 10');
define('_MI_OLEDRION_PRODUCT_PROPERTY_TITLE', 'Title');

define('_MI_OLEDRION_SEARCH_CATEGORY', 'Show category');
define('_MI_OLEDRION_SEARCH_MANUFACTURERS', 'Show Manufacturers');
define('_MI_OLEDRION_SEARCH_VENDORS', 'Show vendors');
define('_MI_OLEDRION_SEARCH_PRICE', 'Show price');
define('_MI_OLEDRION_SEARCH_STOCKS', 'Show stocks');
define('_MI_OLEDRION_SEARCH_PROPERTY1', 'Show property 1');
define('_MI_OLEDRION_SEARCH_PROPERTY2', 'Show property 2');
define('_MI_OLEDRION_SEARCH_PROPERTY3', 'Show property 3');
define('_MI_OLEDRION_SEARCH_PROPERTY4', 'Show property 4');
define('_MI_OLEDRION_SEARCH_PROPERTY5', 'Show property 5');
define('_MI_OLEDRION_SEARCH_PROPERTY6', 'Show property 6');
define('_MI_OLEDRION_SEARCH_PROPERTY7', 'Show property 7');
define('_MI_OLEDRION_SEARCH_PROPERTY8', 'Show property 8');
define('_MI_OLEDRION_SEARCH_PROPERTY9', 'Show property 9');
define('_MI_OLEDRION_SEARCH_PROPERTY10', 'Show property 10');

define('_MI_OLEDRION_CHECKOUT_COUNTRY', 'Show select country');
define('_MI_OLEDRION_CHECKOUT_COUNTRY_DSC', 'if not show, use default selected country on config.php file');
define('_MI_OLEDRION_CHECKOUT_SHIPPING', 'Shipping price type');
define('_MI_OLEDRION_CHECKOUT_SHIPPING_DSC', 'This option just work when your checkout level are Medium or Long');
define('_MI_OLEDRION_CHECKOUT_SHIPPING_1', 'Product shipping price + Location delivery price');
define('_MI_OLEDRION_CHECKOUT_SHIPPING_2', 'Product shipping price');
define('_MI_OLEDRION_CHECKOUT_SHIPPING_3', 'Location delivery price');
define('_MI_OLEDRION_CHECKOUT_SHIPPING_4', 'Free');

define('_MI_OLEDRION_GATEWAY', 'Gateway used by the module');

define('_MI_OLEDRION_ASK_BILL', 'Ask about bill');

define('_MI_OLEDRION_CHECKOUT_LEVEL', 'Checkout level');
define('_MI_OLEDRION_CHECKOUT_LEVEL_1', 'Short - Information, Confirm');
define('_MI_OLEDRION_CHECKOUT_LEVEL_2', 'Medium - Information, location, Delivery, Payment, Confirm');
define('_MI_OLEDRION_CHECKOUT_LEVEL_3', 'Long - Information, Packing, location, Delivery, Payment, Confirm');

define('_MI_OLEDRION_SMS_CHECKOUT', 'Send sms after order checkout?');
define('_MI_OLEDRION_SMS_CHECKOUT_TEXT', 'SMS checkout text');
define('_MI_OLEDRION_SMS_VALIDATE', 'Send SMS after validate order by admin?');
define('_MI_OLEDRION_SMS_VALIDATE_TEXT', 'SMS validate text');

define('_MI_OLEDRION_ADMINGROUPS', 'Groups than have access to all admin parts');
define('_MI_OLEDRION_ADMINGROUPS_DSC', 'Other groups just have access to seleted parts');
define('_MI_OLEDRION_ADMINGROUPS_PARTS', 'Set just allowed parts for access by selected group');

define('_MI_OLEDRION_BREAK_COMMENT_NOTIFICATION', 'Comments and Notifications');
define('_MI_OLEDRION_BREAK_SEARCH', 'Search');
define('_MI_OLEDRION_BREAK_IMAGE', 'Image');
define('_MI_OLEDRION_BREAK_CHECKOUT', 'Checkout');
define('_MI_OLEDRION_BREAK_VIEW', 'View');
define('_MI_OLEDRION_BREAK_MONEY', 'Money');
define('_MI_OLEDRION_BREAK_META', 'Meta');
define('_MI_OLEDRION_BREAK_SMS', 'SMS');

define('_MI_OLEDRION_SMS_PACK', 'SMS Pack');
define('_MI_OLEDRION_SMS_PACK_TEXT', 'SMS Pack Description');
define('_MI_OLEDRION_SMS_SUBMIT', 'SMS Submit');
define('_MI_OLEDRION_SMS_SUBMIT_TEXT', 'SMS Submit Description');
define('_MI_OLEDRION_SMS_DELIVERY', 'SMS Delivery');
define('_MI_OLEDRION_SMS_DELIVERY_TEXT', 'SMS Delivery Description');
define('_MI_OLEDRION_SMS_TRACK', 'SMS Track');
define('_MI_OLEDRION_SMS_TRACK_TEXT', 'SMS Track Description');

//2.34
//Help
define('_MI_OLEDRION_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_OLEDRION_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_OLEDRION_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_OLEDRION_OVERVIEW', 'Overview');

//define('_MI_OLEDRION_HELP_DIR', __DIR__);

//help multi-page
define('_MI_OLEDRION_DISCLAIMER', 'Disclaimer');
define('_MI_OLEDRION_LICENSE', 'License');
define('_MI_OLEDRION_SUPPORT', 'Support');

define('_MI_OLEDRION_SHOW_SAMPLE_BUTTON', 'Show Sample Button?');
define('_MI_OLEDRION_SHOW_SAMPLE_BUTTON_DESC', 'If yes, the "Add Sample Data" button will be visible to the Admin. It is Yes as a default for first installation.');

define('_MI_OLEDRION_FORM_OPTIONS_ADMIN', 'Editor: Admin');
define('_MI_OLEDRION_FORM_OPTIONS_ADMIN_DESC', 'Select the Editor to use by the Admin');
define('_MI_OLEDRION_FORM_OPTIONS_USER', 'Editor: User');
define('_MI_OLEDRION_FORM_OPTIONS_USER_DESC', 'Select the Editor to use by the User');

define('_MI_OLEDRION_HOME', 'Home');
define('_MI_OLEDRION_ABOUT', 'About');
