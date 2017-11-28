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

$moduleDirName = basename(__DIR__);

$modversion['version']       = 2.34;
$modversion['module_status'] = 'Beta 5';
$modversion['release_date']  = '2015/01/15';
$modversion['name']          = _MI_OLEDRION_NAME;
$modversion['description']   = _MI_OLEDRION_DESC;
$modversion['author']        = 'Hervé Thouzard (http://www.herve-thouzard.com/)';
$modversion['credits']       = 'Don Curioso, Voltan, Bezoops, Mariane Antoun, Defkon1, Feichtl, Carlos Pérez, JardaR, Wishcraft, Mamba, and all the other';
$modversion['help']          = 'page=help';
$modversion['license']       = 'GNU GPL 2.0';
$modversion['license_url']   = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['official']      = 0; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']         = 'assets/images/logoModule.png';
$modversion['dirname']       = basename(__DIR__);
// Modules scripts
$modversion['onInstall'] = 'include/functions_install.php';
//$modversion['onUpdate']  = 'include/functions_update.php';
//icons
//$modversion['dirmoduleadmin']      = '/Frameworks/moduleclasses/moduleadmin';
//$modversion['icons16']             = '../../Frameworks/moduleclasses/icons/16';
//$modversion['icons32']             = '../../Frameworks/moduleclasses/icons/32';
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

// ------------------- Mysql ------------------- //
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'] = [
    $moduleDirName . '_' . 'manufacturer',
    $moduleDirName . '_' . 'products',
    $moduleDirName . '_' . 'productsmanu',
    $moduleDirName . '_' . 'caddy',
    $moduleDirName . '_' . 'cat',
    $moduleDirName . '_' . 'commands',
    $moduleDirName . '_' . 'related',
    $moduleDirName . '_' . 'vat',
    $moduleDirName . '_' . 'votedata',
    $moduleDirName . '_' . 'discounts',
    $moduleDirName . '_' . 'vendors',
    $moduleDirName . '_' . 'files',
    $moduleDirName . '_' . 'persistent_cart',
    $moduleDirName . '_' . 'gateways_options',
    $moduleDirName . '_' . 'lists',
    $moduleDirName . '_' . 'products_list',
    $moduleDirName . '_' . 'attributes',
    $moduleDirName . '_' . 'caddy_attributes',
    $moduleDirName . '_' . 'packing',
    $moduleDirName . '_' . 'location',
    $moduleDirName . '_' . 'delivery',
    $moduleDirName . '_' . 'payment',
    $moduleDirName . '_' . 'location_delivery',
    $moduleDirName . '_' . 'delivery_payment',
    $moduleDirName . '_' . 'payment_log',
];

$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_OLEDRION_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_OLEDRION_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_OLEDRION_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_OLEDRION_SUPPORT, 'link' => 'page=support'],
];

// Blocks

/**
 * Recent products block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_new.php',
    'name'        => _MI_OLEDRION_BNAME1,
    'description' => _MI_OLEDRION_BNAME1_DESC,
    'show_func'   => 'b_oledrion_new_show',
    'edit_func'   => 'b_oledrion_new_edit',
    'options'     => '10|0|0', // Voir 10 produits, pour toutes les catégories, uniquement les produits du mois ?
    'template'    => 'oledrion_block_new.tpl',
];
/**
 * Most viewed products block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_top.php',
    'name'        => _MI_OLEDRION_BNAME2,
    'description' => _MI_OLEDRION_BNAME2_DESC,
    'show_func'   => 'b_oledrion_top_show',
    'edit_func'   => 'b_oledrion_top_edit',
    'options'     => '10|0',
    'template'    => 'oledrion_block_top.tpl',
];
/**
 * Categories block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_categories.php',
    'name'        => _MI_OLEDRION_BNAME3,
    'description' => _MI_OLEDRION_BNAME3_DESC,
    'show_func'   => 'b_oledrion_category_show',
    'edit_func'   => 'b_oledrion_category_edit',
    'options'     => '0', // 0 = en relation avec la page, 1=classique, 2=Déplié
    'template'    => 'oledrion_block_categories.tpl',
];
/**
 * Best sellers block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_best_sales.php',
    'name'        => _MI_OLEDRION_BNAME4,
    'description' => _MI_OLEDRION_BNAME4_DESC,
    'show_func'   => 'b_oledrion_bestsales_show',
    'edit_func'   => 'b_oledrion_bestsales_edit',
    'options'     => '10|0', // Voir 10 produits, pour toutes les catégories
    'template'    => 'oledrion_block_bestsales.tpl',
];
/**
 * Top rated products
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_rated.php',
    'name'        => _MI_OLEDRION_BNAME5,
    'description' => _MI_OLEDRION_BNAME5_DESC,
    'show_func'   => 'b_oledrion_rated_show',
    'edit_func'   => 'b_oledrion_rated_edit',
    'options'     => '10|0',
    'template'    => 'oledrion_block_rated.tpl',
];
/**
 * Random product(s) block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_random.php',
    'name'        => _MI_OLEDRION_BNAME6,
    'description' => _MI_OLEDRION_BNAME6_DESC,
    'show_func'   => 'b_oledrion_random_show',
    'edit_func'   => 'b_oledrion_random_edit',
    'options'     => '1|0|0', // Nombre de produits, catégorie, produits du mois uniquement ?
    'template'    => 'oledrion_block_random.tpl',
];
/**
 * Products with a discount block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_promotion.php',
    'name'        => _MI_OLEDRION_BNAME7,
    'description' => _MI_OLEDRION_BNAME7_DESC,
    'show_func'   => 'b_oledrion_promotion_show',
    'edit_func'   => 'b_oledrion_promotion_edit',
    'options'     => '10|0',
    'template'    => 'oledrion_block_promotion.tpl',
];
/**
 * Cart block
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_cart.php',
    'name'        => _MI_OLEDRION_BNAME8,
    'description' => _MI_OLEDRION_BNAME8_DESC,
    'show_func'   => 'b_oledrion_cart_show',
    'edit_func'   => 'b_oledrion_cart_edit',
    'options'     => '4', // Maximum count of items to show
    'template'    => 'oledrion_block_cart.tpl',
];
/**
 * Recommended products
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_recommended.php',
    'name'        => _MI_OLEDRION_BNAME9,
    'description' => _MI_OLEDRION_BNAME9_DESC,
    'show_func'   => 'b_oledrion_recomm_show',
    'edit_func'   => 'b_oledrion_recomm_edit',
    'options'     => '10|0',
    'template'    => 'oledrion_block_recommended.tpl',
];
/**
 * Recently Sold
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_recentlysold.php',
    'name'        => _MI_OLEDRION_BNAME10,
    'description' => _MI_OLEDRION_BNAME10_DESC,
    'show_func'   => 'b_oledrion_recentlysold_show',
    'edit_func'   => 'b_oledrion_recentlysold_edit',
    'options'     => '10', // Nombre maximum de produits à voir
    'template'    => 'oledrion_block_recentlysold.tpl',
];
/**
 * Last lists
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_recent_lists.php',
    'name'        => _MI_OLEDRION_BNAME11,
    'description' => _MI_OLEDRION_BNAME11_DESC,
    'show_func'   => 'b_oledrion_recent_lists_show',
    'edit_func'   => 'b_oledrion_recent_lists_edit',
    'options'     => '10|0', // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
    'template'    => 'oledrion_block_recent_lists.tpl',
];
/**
 * My lists
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_my_lists.php',
    'name'        => _MI_OLEDRION_BNAME12,
    'description' => _MI_OLEDRION_BNAME12_DESC,
    'show_func'   => 'b_oledrion_my_lists_show',
    'edit_func'   => 'b_oledrion_my_lists_edit',
    'options'     => '10', // Nombre maximum de listes à afficher
    'template'    => 'oledrion_block_my_lists.tpl',
];
/**
 * Lists of the current category ("according to the current category")
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_categoy_lists.php',
    'name'        => _MI_OLEDRION_BNAME13,
    'description' => _MI_OLEDRION_BNAME13_DESC,
    'show_func'   => 'b_oledrion_category_lists_show',
    'edit_func'   => 'b_oledrion_category_lists_edit',
    'options'     => '10|0', // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
    'template'    => 'oledrion_block_category_lists.tpl',
];
/**
 * Random lists
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_random_lists.php',
    'name'        => _MI_OLEDRION_BNAME14,
    'description' => _MI_OLEDRION_BNAME14_DESC,
    'show_func'   => 'b_oledrion_random_lists_show',
    'edit_func'   => 'b_oledrion_random_lists_edit',
    'options'     => '10|0', // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
    'template'    => 'oledrion_block_random_lists.tpl',
];
/**
 * Most viewed lists
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_mostviewed_lists.php',
    'name'        => _MI_OLEDRION_BNAME15,
    'description' => _MI_OLEDRION_BNAME15_DESC,
    'show_func'   => 'b_oledrion_mostviewed_lists_show',
    'edit_func'   => 'b_oledrion__mostviewed_lists_edit',
    'options'     => '10|0', // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
    'template'    => 'oledrion_block_mostviewed_lists.tpl',
];
/**
 * Ajax search
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_ajax_search.php',
    'name'        => _MI_OLEDRION_BNAME16,
    'description' => _MI_OLEDRION_BNAME16_DESC,
    'show_func'   => 'b_oledrion_ajax_search_show',
    'edit_func'   => 'b_oledrion__ajax_search_edit',
    'options'     => '1',
    'template'    => 'oledrion_block_ajax_search.tpl',
];
/*
 * $options:
 *                  $options[0] - number of tags to display
 *                  $options[1] - time duration, in days, 0 for all the time
 *                  $options[2] - max font size (px or %)
 *                  $options[3] - min font size (px or %)
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_block_tag.php',
    'name'        => _MI_OLEDRION_TAG_CLOUD,
    'description' => 'Show tag cloud',
    'show_func'   => 'oledrion_tag_block_cloud_show',
    'edit_func'   => 'oledrion_tag_block_cloud_edit',
    'options'     => '100|0|150|80',
    'template'    => 'oledrion_tag_block_cloud.tpl'
];

/*
 * $options:
 *                  $options[0] - number of tags to display
 *                  $options[1] - time duration, in days, 0 for all the time
 *                  $options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion['blocks'][] = [
    'file'        => 'oledrion_block_tag.php',
    'name'        => _MI_OLEDRION_TOP_TAGS,
    'description' => 'Show top tags',
    'show_func'   => 'oledrion_tag_block_top_show',
    'edit_func'   => 'oledrion_tag_block_top_edit',
    'options'     => '50|30|c',
    'template'    => 'oledrion_tag_block_top.tpl'
];

// Menu
$modversion['hasMain'] = 1;
$cptm                  = 0;
require_once __DIR__ . '/class/Utility.php';
if (\Xoopsmodules\oledrion\Utility::getModuleOption('use_price')) {
    ++$cptm;
    $modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME1;
    $modversion['sub'][$cptm]['url']  = 'caddy.php';
}

++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME3;
$modversion['sub'][$cptm]['url']  = 'category.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME4;
$modversion['sub'][$cptm]['url']  = 'categories-map.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME5;
$modversion['sub'][$cptm]['url']  = 'whoswho.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME6;
$modversion['sub'][$cptm]['url']  = 'all-products.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME9;
$modversion['sub'][$cptm]['url']  = 'recommended.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME7;
$modversion['sub'][$cptm]['url']  = 'search.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME8;
$modversion['sub'][$cptm]['url']  = 'cgv.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME10;
$modversion['sub'][$cptm]['url']  = 'my-lists.php';
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME11;
$modversion['sub'][$cptm]['url']  = 'all-lists.php';

// Adding parent categories in submenu ********************************************************
global $xoopsModule;
if (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname']
    && $xoopsModule->getVar('isactive')) {
    if (!isset($h_oledrion_cat)) {
        $h_oledrion_cat = xoops_getModuleHandler('oledrion_cat', 'oledrion');
    }
    $categories = $h_oledrion_cat->getMotherCategories();
    foreach ($categories as $category) {
        ++$cptm;
        $modversion['sub'][$cptm]['name'] = $category->getVar('cat_title');
        $modversion['sub'][$cptm]['url']  = basename($category->getLink());
    }
}

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'oledrion_search';

// Comments
$modversion['hasComments']          = 1;
$modversion['comments']['itemName'] = 'product_id';
$modversion['comments']['pageName'] = 'product.php';

// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'oledrion_com_approve';
$modversion['comments']['callback']['update']  = 'oledrion_com_update';

// Templates
$cptt = 0;

$modversion['templates'] = [
    ['file' => 'oledrion_product_price.tpl', 'description' => "Used in Ajax to display product's price"],
    ['file' => 'oledrion_chunk.tpl', 'description' => ''],
    ['file' => 'oledrion_categories_list.tpl', 'description' => ''],
    ['file' => 'oledrion_index.tpl', 'description' => ''],
    ['file' => 'oledrion_category.tpl', 'description' => ''],
    ['file' => 'oledrion_product.tpl', 'description' => ''],
    ['file' => 'oledrion_bill.tpl', 'description' => ''],
    ['file' => 'oledrion_caddy.tpl', 'description' => ''],
    ['file' => 'oledrion_command.tpl', 'description' => ''],
    ['file' => 'oledrion_thankyou.tpl', 'description' => ''],
    ['file' => 'oledrion_cgv.tpl', 'description' => 'General Conditions Of Sale'],
    ['file' => 'oledrion_search.tpl', 'description' => ''],
    ['file' => 'oledrion_rss.tpl', 'description' => ''],
    ['file' => 'oledrion_map.tpl', 'description' => ''],
    ['file' => 'oledrion_whoswho.tpl', 'description' => ''],
    ['file' => 'oledrion_allproducts.tpl', 'description' => ''],
    ['file' => 'oledrion_manufacturer.tpl', 'description' => ''],
    ['file' => 'oledrion_rate_product.tpl', 'description' => ''],
    ['file' => 'oledrion_pdf_catalog.tpl', 'description' => ''],
    ['file' => 'oledrion_purchaseorder.tpl', 'description' => ''],
    ['file' => 'oledrion_cancelpurchase.tpl', 'description' => ''],
    ['file' => 'oledrion_recommended.tpl', 'description' => 'Latest recommended products'],
    ['file' => 'oledrion_admin_discounts.tpl', 'description' => ''],
    ['file' => 'oledrion_attribute_radio.tpl', 'description' => 'Template for attributes of type radio'],
    ['file' => 'oledrion_attribute_checkbox.tpl', 'description' => 'Template for attributes of type checkbox'],
    ['file' => 'oledrion_attribute_select.tpl', 'description' => 'Template for attributes of type select (listbox)'],
    ['file' => 'oledrion_all_lists.tpl', 'description' => 'List of all lists'],
    ['file' => 'oledrion_list.tpl', 'description' => 'Show a list content'],
    ['file' => 'oledrion_mylists.tpl', 'description' => 'Enable user to manage his/her lists'],
    ['file' => 'oledrion_productsselector.tpl', 'description' => 'Used to select products'],
    ['file' => 'oledrion_product_box.tpl', 'description' => 'Product box'],
    ['file' => 'oledrion_product_print.tpl', 'description' => 'Product print'],
    ['file' => 'oledrion_bill_print.tpl', 'description' => 'Bill print'],
    ['file' => 'oledrion_user.tpl', 'description' => 'User page'],
];
// ********************************************************************************************************************
// ****************************************** SETTINGS ****************************************************************
// ********************************************************************************************************************
// Load class
xoops_load('xoopslists');

/**
 * Do you want to use URL rewriting ?
 */

$modversion['config'][] = [
    'name'        => 'urlrewriting',
    'title'       => '_MI_OLEDRION_URL_REWR',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Editor to use
 */
//$modversion['config'][] = [
//    'name'        => 'bl_form_options',
//    'title'       => '_MI_OLEDRION_FORM_OPTIONS',
//    'description' => '_MI_OLEDRION_FORM_OPTIONS_DESC',
//    'formtype'    => 'select',
//    'valuetype'   => 'text',
//    'options'     => XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor'),
//    'default'     => 'dhtmltextarea',
//];


$modversion['config'][] = [
    'name' => 'editorAdmin',
    'title' => '_MI_OLEDRION_FORM_OPTIONS_ADMIN',
    'description' => '_MI_OLEDRION_FORM_OPTIONS_ADMIN_DESC',
    'formtype' => 'select',
    'valuetype' => 'text',
    'options' => XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor'),
    'default' => 'tinymce'
];

$modversion['config'][] = [
    'name' => 'editorUser',
    'title' => '_MI_OLEDRION_FORM_OPTIONS_USER',
    'description' => '_MI_OLEDRION_FORM_OPTIONS_USER_DESC',
    'formtype' => 'select',
    'valuetype' => 'text',
    'options' => XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor'),
    'default' => 'dhtmltextarea'
];

/**
 * Tooltips, or infotips are some small textes you can see when you
 * move your mouse over an article's title. This text contains the
 * first (x) characters of the story
 */
$modversion['config'][] = [
    'name'        => 'infotips',
    'title'       => '_MI_OLEDRION_INFOTIPS',
    'description' => '_MI_OLEDRION_INFOTIPS_DES',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '0',
];

/**
 * MAX Filesize Upload in kilo bytes
 */

$modversion['config'][] = [
    'name'        => 'maxuploadsize',
    'title'       => '_MI_OLEDRION_UPLOADFILESIZE',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10485760,
];

/**
 * Do you want to enable your visitors to rate products ?
 */

$modversion['config'][] = [
    'name'        => 'rateproducts',
    'title'       => '_MI_OLEDRION_RATE',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Global module's Advertisement
 */
$modversion['config'][] = [
    'name'        => 'advertisement',
    'title'       => '_MI_OLEDRION_ADVERTISEMENT',
    'description' => '_MI_OLEDRION_ADV_DESCR',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

/**
 * Mime Types
 * Default values : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
$modversion['config'][] = [
    'name'        => 'mimetypes',
    'title'       => '_MI_OLEDRION_MIMETYPES',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar",
];

/**
 * Group of users to which send an email when a product's stock is low (if nothing is typed then there's no alert)
 */
$modversion['config'][] = [
    'name'        => 'stock_alert_email',
    'title'       => '_MI_OLEDRION_STOCK_EMAIL',
    'description' => '_MI_OLEDRION_STOCK_EMAIL_DSC',
    'formtype'    => 'group',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Group of users to wich send an email when a product is sold
 */
$modversion['config'][] = [
    'name'        => 'grp_sold',
    'title'       => '_MI_OLEDRION_GRP_SOLD',
    'description' => '',
    'formtype'    => 'group',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Group of users authorized to modify products quantities from the product page
 */
$modversion['config'][] = [
    'name'        => 'grp_qty',
    'title'       => '_MI_OLEDRION_GRP_QTY',
    'description' => '',
    'formtype'    => 'group',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Use RSS Feeds ?
 */
$modversion['config'][] = [
    'name'        => 'use_rss',
    'title'       => '_MI_OLEDRION_OPT7',
    'description' => '_MI_OLEDRION_OPT7_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Enable PDF Catalog ?
 */
$modversion['config'][] = [
    'name'        => 'pdf_catalog',
    'title'       => '_MI_OLEDRION_PDF_CATALOG',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Use the price field ?
 */
$modversion['config'][] = [
    'name'        => 'use_price',
    'title'       => '_MI_OLEDRION_USE_PRICE',
    'description' => '_MI_OLEDRION_USE_PRICE_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Multiply Shipping by product's quantity ?
 */
$modversion['config'][] = [
    'name'        => 'shipping_quantity',
    'title'       => '_MI_OLEDRION_SHIPPING_QUANTITY',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Use tags ?
 */
$modversion['config'][] = [
    'name'        => 'use_tags',
    'title'       => '_MI_OLEDRION_USE_TAGS',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Use stocks in products attributes ?
 *
 * @since 2.3.2009.03.11
 */
$modversion['config'][] = [
    'name'        => 'attributes_stocks',
    'title'       => '_MI_OLEDRION_USE_STOCK_ATTRIBUTES',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

// Get Admin groups
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('group_type', 'Admin'));
$memberHandler     = xoops_getHandler('member');
$admin_xoopsgroups = $memberHandler->getGroupList($criteria);
foreach ($admin_xoopsgroups as $key => $admin_group) {
    $admin_groups[$admin_group] = $key;
}

$modversion['config'][] = [
    'name'        => 'admin_groups',
    'title'       => '_MI_OLEDRION_ADMINGROUPS',
    'description' => '_MI_OLEDRION_ADMINGROUPS_DSC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'options'     => $admin_groups,
    'default'     => $admin_groups,
];

$modversion['config'][] = [
    'name'        => 'admin_groups_part',
    'title'       => '_MI_OLEDRION_ADMINGROUPS_PARTS',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => 'attributes|delivery|gateways|location|manufacturers|packing|property|vendors|categories|discounts|lowstock|newsletter|payment|texts|dashboard|files|lists|maintain|products|vat',
];

$modversion['config'][] = [
    'name'        => 'breakmeta',
    'title'       => '_MI_OLEDRION_BREAK_META',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

/**
 * Enter meta data manually ?
 */
$modversion['config'][] = [
    'name'        => 'manual_meta',
    'title'       => '_MI_OLEDRION_MANUAL_META',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * METAGEN, Max count of keywords to create
 */
$modversion['config'][] = [
    'name'        => 'metagen_maxwords',
    'title'       => '_MI_OLEDRION_OPT23',
    'description' => '_MI_OLEDRION_OPT23_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 40,
];

/**
 * METAGEN - Keywords order
 */
$modversion['config'][] = [
    'name'        => 'metagen_order',
    'title'       => '_MI_OLEDRION_OPT24',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '_MI_OLEDRION_OPT241' => 0,
        '_MI_OLEDRION_OPT242' => 1,
        '_MI_OLEDRION_OPT243' => 2,
    ]
];

/**
 * METAGEN - Black list
 */

$modversion['config'][] = [
    'name'        => 'metagen_blacklist',
    'title'       => '_MI_OLEDRION_OPT25',
    'description' => '_MI_OLEDRION_OPT25_DSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'break_money',
    'title'       => '_MI_OLEDRION_BREAK_MONEY',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

/**
 * Money, full label
 */
$modversion['config'][] = [
    'name'        => 'money_full',
    'title'       => '_MI_OLEDRION_MONEY_F',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_OLEDRION_SETTING_1,
];

/**
 * Money, short label
 */
$modversion['config'][] = [
    'name'        => 'money_short',
    'title'       => '_MI_OLEDRION_MONEY_S',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_OLEDRION_SETTING_2,
];

/**
 * Decimals count
 */
$modversion['config'][] = [
    'name'        => 'decimals_count',
    'title'       => '_MI_OLEDRION_DECIMAL',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => _MI_OLEDRION_SETTING_3,
];

/**
 * Monnaie's place (left or right) ?
 */
$modversion['config'][] = [
    'name'        => 'monnaie_place',
    'title'       => '_MI_OLEDRION_CONF00',
    'description' => '_MI_OLEDRION_CONF00_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => _MI_OLEDRION_SETTING_4,
];

/**
 * Thousands separator
 */
$modversion['config'][] = [
    'name'        => 'thousands_sep',
    'title'       => '_MI_OLEDRION_CONF04',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_OLEDRION_SETTING_5,
];

/**
 * Decimal separator
 */
$modversion['config'][] = [
    'name'        => 'decimal_sep',
    'title'       => '_MI_OLEDRION_CONF05',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_OLEDRION_SETTING_6,
];

$modversion['config'][] = [
    'name'        => 'break_view',
    'title'       => '_MI_OLEDRION_BREAK_VIEW',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

$modversion['config'][] = [
    'name'        => 'newproducts',
    'title'       => '_MI_OLEDRION_NEWLINKS',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

$modversion['config'][] = [
    'name'        => 'perpage',
    'title'       => '_MI_OLEDRION_PERPAGE',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

$modversion['config'][] = [
    'name'        => 'related_limit',
    'title'       => '_MI_OLEDRION_RELATEDLIMIT',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

$modversion['config'][] = [
    'name'        => 'index_colums',
    'title'       => '_MI_OLEDRION_COLUMNS_INDEX',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'category_colums',
    'title'       => '_MI_OLEDRION_COLUMNS_CATEGORY',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'max_products',
    'title'       => '_MI_OLEDRION_ADAPTED_LIST',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 200,
];

/**
 * Display a summary table of the last published products (in all categories) ?
 */
$modversion['config'][] = [
    'name'        => 'summarylast',
    'title'       => '_MI_OLEDRION_SUMMARY1_SHOW',
    'description' => '_MI_OLEDRION_SUMMARY1_SHOW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

/**
 * Display a summary table of the last published products in the same category ?
 */
$modversion['config'][] = [
    'name'        => 'summarycategory',
    'title'       => '_MI_OLEDRION_SUMMARY2_SHOW',
    'description' => '_MI_OLEDRION_SUMMARY2_SHOW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
];

/**
 * Better Together ?
 */
$modversion['config'][] = [
    'name'        => 'better_together',
    'title'       => '_MI_OLEDRION_BEST_TOGETHER',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Display unpublished products ?
 */
$modversion['config'][] = [
    'name'        => 'show_unpublished',
    'title'       => '_MI_OLEDRION_UNPUBLISHED',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * If you set this option to yes then you will see two links at the bottom
 * of each item. The first link will enable you to go to the previous
 * item and the other link will bring you to the next item
 */
$modversion['config'][] = [
    'name'        => 'showprevnextlink',
    'title'       => '_MI_OLEDRION_PREVNEX_LINK',
    'description' => '_MI_OLEDRION_PREVNEX_LINK_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Display products when there are no more products ?
 */
$modversion['config'][] = [
    'name'        => 'nostock_display',
    'title'       => '_MI_OLEDRION_NO_MORE',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Message to display when there's not more quantity for a product ?
 */
$modversion['config'][] = [
    'name'        => 'nostock_msg',
    'title'       => '_MI_OLEDRION_MSG_NOMORE',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

/**
 * Count of visible items in the module's administration
 */
$modversion['config'][] = [
    'name'        => 'items_count',
    'title'       => '_MI_OLEDRION_ITEMSCNT',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 30,
];

$modversion['config'][] = [
    'name'        => 'break_image',
    'title'       => '_MI_OLEDRION_BREAK_IMAGE',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

/**
 * Do you want to automatically resize the main picture of each product ?
 */
$modversion['config'][] = [
    'name'        => 'resize_main',
    'title'       => '_MI_OLEDRION_RESIZE_MAIN',
    'description' => '_MI_OLEDRION_RESIZE_MAIN_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Create thumbs automatically ?
 */
$modversion['config'][] = [
    'name'        => 'create_thumbs',
    'title'       => '_MI_OLEDRION_CREATE_THUMBS',
    'description' => '_MI_OLEDRION_CREATE_THUMBS_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Images width
 */
$modversion['config'][] = [
    'name'        => 'images_width',
    'title'       => '_MI_OLEDRION_IMAGES_WIDTH',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 400,
];

/**
 * Images height
 */
$modversion['config'][] = [
    'name'        => 'images_height',
    'title'       => '_MI_OLEDRION_IMAGES_HEIGHT',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 400,
];

/**
 * Thumbs width
 */
$modversion['config'][] = [
    'name'        => 'thumbs_width',
    'title'       => '_MI_OLEDRION_THUMBS_WIDTH',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 180,
];

/**
 * Thumbs height
 */
$modversion['config'][] = [
    'name'        => 'thumbs_height',
    'title'       => '_MI_OLEDRION_THUMBS_HEIGHT',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 180,
];

/**
 * Do you also want to resize categories'pictures to the above dimensions ?
 */
$modversion['config'][] = [
    'name'        => 'resize_others',
    'title'       => '_MI_OLEDRION_RESIZE_CATEGORIES',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'break_search',
    'title'       => '_MI_OLEDRION_BREAK_SEARCH',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

$modversion['config'][] = [
    'name'        => 'search_category',
    'title'       => '_MI_OLEDRION_SEARCH_CATEGORY',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_manufacturers',
    'title'       => '_MI_OLEDRION_SEARCH_MANUFACTURERS',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_vendors',
    'title'       => '_MI_OLEDRION_SEARCH_VENDORS',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_price',
    'title'       => '_MI_OLEDRION_SEARCH_PRICE',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_stocks',
    'title'       => '_MI_OLEDRION_SEARCH_STOCKS',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property1',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY1',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property2',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY2',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property3',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY3',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property4',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY4',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property5',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY5',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property6',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY6',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property7',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY7',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property8',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY8',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property9',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY9',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'search_property10',
    'title'       => '_MI_OLEDRION_SEARCH_PROPERTY10',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'break_checkout',
    'title'       => '_MI_OLEDRION_BREAK_CHECKOUT',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

/**
 * Use the persistent cart ?
 */
$modversion['config'][] = [
    'name'        => 'persistent_cart',
    'title'       => '_MI_OLEDRION_PERSISTENT_CART',
    'description' => '_MI_OLEDRION_PERSISTENT_CART_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Restrict orders to registred users ?
 */
$modversion['config'][] = [
    'name'        => 'restrict_orders',
    'title'       => '_MI_OLEDRION_RESTRICT_ORDERS',
    'description' => '_MI_OLEDRION_RESTRICT_ORDERS_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Enable offline payment ?
 */
$modversion['config'][] = [
    'name'        => 'offline_payment',
    'title'       => '_MI_OLEDRION_OFFLINE_PAYMENT',
    'description' => '_MI_OLEDRION_OFF_PAY_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'checkout_level',
    'title'       => '_MI_OLEDRION_CHECKOUT_LEVEL',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [
        '_MI_OLEDRION_CHECKOUT_LEVEL_1' => 1,
        '_MI_OLEDRION_CHECKOUT_LEVEL_2' => 2,
        '_MI_OLEDRION_CHECKOUT_LEVEL_3' => 3
    ]
];

$modversion['config'][] = [
    'name'        => 'checkout_country',
    'title'       => '_MI_OLEDRION_CHECKOUT_COUNTRY',
    'description' => '_MI_OLEDRION_CHECKOUT_COUNTRY_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'checkout_shipping',
    'title'       => '_MI_OLEDRION_CHECKOUT_SHIPPING',
    'description' => '_MI_OLEDRION_CHECKOUT_SHIPPING_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [
        '_MI_OLEDRION_CHECKOUT_SHIPPING_1' => 1,
        '_MI_OLEDRION_CHECKOUT_SHIPPING_2' => 2,
        '_MI_OLEDRION_CHECKOUT_SHIPPING_3' => 3,
        '_MI_OLEDRION_CHECKOUT_SHIPPING_4' => 4,
    ]
];

/**
 * Ask for VAT number ?
 *
 * @since 2.3.2009.03.09
 */
$modversion['config'][] = [
    'name'        => 'ask_vatnumber',
    'title'       => '_MI_OLEDRION_ASK_VAT_NUMBER',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'ask_bill',
    'title'       => '_MI_OLEDRION_ASK_BILL',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'break_sms',
    'title'       => '_MI_OLEDRION_BREAK_SMS',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

$modversion['config'][] = [
    'name'        => 'sms_checkout',
    'title'       => '_MI_OLEDRION_SMS_CHECKOUT',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_checkout_text',
    'title'       => '_MI_OLEDRION_SMS_CHECKOUT_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'sms_validate',
    'title'       => '_MI_OLEDRION_SMS_VALIDATE',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_validate_text',
    'title'       => '_MI_OLEDRION_SMS_VALIDATE_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'sms_pack',
    'title'       => '_MI_OLEDRION_SMS_PACK',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_pack_text',
    'title'       => '_MI_OLEDRION_SMS_PACK_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'sms_submit',
    'title'       => '_MI_OLEDRION_SMS_SUBMIT',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_submit_text',
    'title'       => '_MI_OLEDRION_SMS_SUBMIT_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'sms_delivery',
    'title'       => '_MI_OLEDRION_SMS_DELIVERY',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_delivery_text',
    'title'       => '_MI_OLEDRION_SMS_DELIVERY_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'sms_track',
    'title'       => '_MI_OLEDRION_SMS_TRACK',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'sms_track_text',
    'title'       => '_MI_OLEDRION_SMS_TRACK_TEXT',
    'description' => '',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'break_comment',
    'title'       => '_MI_OLEDRION_BREAK_COMMENT_NOTIFICATION',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'head',
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'showsamplebutton',
    'title'       => '_MI_OLEDRION_SHOW_SAMPLE_BUTTON',
    'description' => '_MI_OLEDRION_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

// ************************************************************************************************
// ************************* Hidden settings ******************************************************
// ************************************************************************************************

$modversion['config'][] = [
    'name'        => 'chunk1',
    'title'       => '_MI_OLEDRION_CHUNK1',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'chunk2',
    'title'       => '_MI_OLEDRION_CHUNK2',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'int',
    'default'     => 2,
];

$modversion['config'][] = [
    'name'        => 'chunk3',
    'title'       => '_MI_OLEDRION_CHUNK3',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'int',
    'default'     => 3,
];

$modversion['config'][] = [
    'name'        => 'chunk4',
    'title'       => '_MI_OLEDRION_CHUNK4',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'int',
    'default'     => 4,
];

$modversion['config'][] = [
    'name'        => 'used_gateway',
    'title'       => '_MI_OLEDRION_GATEWAY',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'paypal',
];

$modversion['config'][] = [
    'name'        => 'product_property1',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY1',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property1_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 1',
];

$modversion['config'][] = [
    'name'        => 'product_property2',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY2',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property2_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 2',
];

$modversion['config'][] = [
    'name'        => 'product_property3',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY3',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property3_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 3',
];

$modversion['config'][] = [
    'name'        => 'product_property4',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY4',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property4_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 4',
];

$modversion['config'][] = [
    'name'        => 'product_property5',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY5',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property5_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 5',
];

$modversion['config'][] = [
    'name'        => 'product_property6',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY6',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property6_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 6',
];

$modversion['config'][] = [
    'name'        => 'product_property7',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY7',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property7_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 7',
];

$modversion['config'][] = [
    'name'        => 'product_property8',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY8',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property8_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 8',
];

$modversion['config'][] = [
    'name'        => 'product_property9',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY9',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property9_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 9',
];

$modversion['config'][] = [
    'name'        => 'product_property10',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY10',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
];

$modversion['config'][] = [
    'name'        => 'product_property10_title',
    'title'       => '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE',
    'description' => '',
    'formtype'    => 'hidden',
    'valuetype'   => 'text',
    'default'     => 'property 10',
];

// ************************************************************************************************
// Notifications **********************************************************************************
// ************************************************************************************************
$modversion['hasNotification']             = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'oledrion_notify_iteminfo';

$modversion['notification']['category'][1]['name']           = 'global';
$modversion['notification']['category'][1]['title']          = _MI_OLEDRION_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description']    = _MI_OLEDRION_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = [
    'main.php',
    'category.php',
    'product.php',
    'categories-map.php',
    'all-products.php'
];

$modversion['notification']['event'][1]['name']          = 'new_category';
$modversion['notification']['event'][1]['category']      = 'global';
$modversion['notification']['event'][1]['title']         = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFY;
$modversion['notification']['event'][1]['caption']       = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYCAP;
$modversion['notification']['event'][1]['description']   = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][1]['mail_subject']  = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYSBJ;

$modversion['notification']['event'][2]['name']          = 'new_product';
$modversion['notification']['event'][2]['category']      = 'global';
$modversion['notification']['event'][2]['title']         = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFY;
$modversion['notification']['event'][2]['caption']       = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYCAP;
$modversion['notification']['event'][2]['description']   = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_newproduct_notify';
$modversion['notification']['event'][2]['mail_subject']  = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYSBJ;
