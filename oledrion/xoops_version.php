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
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 * @version     $Id$
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$modversion['name']        = _MI_OLEDRION_NAME;
$modversion['version']     = 2.34;
$modversion['description'] = _MI_OLEDRION_DESC;
$modversion['author']      = "Hervé Thouzard (http://www.herve-thouzard.com/)";
$modversion['credits']     = "Don Curioso, Voltan, Bezoops, Mariane Antoun, Defkon1, Feichtl, Carlos Pérez, JardaR, Wishcraft, Mamba, and all the other";
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0';
$modversion['license_url'] = "www.gnu.org/licenses/gpl-2.0.html/";
$modversion['official']    = 0;
$modversion['image']       = 'assets/images/oledrion_logo.png';
$modversion['dirname']     = basename(dirname(__FILE__));
// Modules scripts
$modversion['onInstall'] = 'include/functions_install.php';
$modversion['onUpdate']  = 'include/functions_update.php';
//icons
$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16']        = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32']        = '../../Frameworks/moduleclasses/icons/32';
//about
$modversion['release_date']        = '2012/12/21';
$modversion["module_website_url"]  = "www.xoops.org";
$modversion["module_website_name"] = "XOOPS";
$modversion["module_status"]       = "Beta 4";
$modversion['min_php']             = '5.3.7';
$modversion['min_xoops']           = "2.5.7";
$modversion['min_admin']           = '1.1';
$modversion['min_db']              = array(
    'mysql'  => '5.0.7',
    'mysqli' => '5.0.7'
);

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'oledrion_manufacturer';
$modversion['tables'][1] = 'oledrion_products';
$modversion['tables'][2] = 'oledrion_productsmanu';
$modversion['tables'][3] = 'oledrion_caddy';
$modversion['tables'][4] = 'oledrion_cat';
$modversion['tables'][5] = 'oledrion_commands';
$modversion['tables'][6] = 'oledrion_related';
$modversion['tables'][7] = 'oledrion_vat';
$modversion['tables'][8] = 'oledrion_votedata';
$modversion['tables'][9] = 'oledrion_discounts';
$modversion['tables'][10] = 'oledrion_vendors';
$modversion['tables'][11] = 'oledrion_files';
$modversion['tables'][12] = 'oledrion_persistent_cart';
$modversion['tables'][13] = 'oledrion_gateways_options';
$modversion['tables'][14] = 'oledrion_lists';
$modversion['tables'][15] = 'oledrion_products_list';
$modversion['tables'][16] = 'oledrion_attributes';
$modversion['tables'][17] = 'oledrion_caddy_attributes';
$modversion['tables'][18] = 'oledrion_packing';
$modversion['tables'][19] = 'oledrion_location';
$modversion['tables'][20] = 'oledrion_delivery';
$modversion['tables'][21] = 'oledrion_payment';
$modversion['tables'][22] = 'oledrion_location_delivery';
$modversion['tables'][23] = 'oledrion_delivery_payment';
$modversion['tables'][24] = 'oledrion_payment_log';

$modversion['hasAdmin'] = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Blocks
$cptb = 0;

/**
 * Recent products block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_new.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME1;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME1_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_new_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_new_edit';
$modversion['blocks'][$cptb]['options'] = '10|0|0'; // Voir 10 produits, pour toutes les catégories, uniquement les produits du mois ?
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_new.tpl';

/**
 * Most viewed products block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_top.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME2;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME2_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_top_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_top_edit';
$modversion['blocks'][$cptb]['options'] = '10|0';
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_top.tpl';

/**
 * Categories block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_categories.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME3;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME3_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_category_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_category_edit';
$modversion['blocks'][$cptb]['options'] = '0'; // 0 = en relation avec la page, 1=classique, 2=Déplié
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_categories.tpl';

/**
 * Best sellers block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_best_sales.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME4;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME4_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_bestsales_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_bestsales_edit';
$modversion['blocks'][$cptb]['options'] = '10|0'; // Voir 10 produits, pour toutes les catégories
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_bestsales.tpl';

/**
 * Top rated products
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_rated.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME5;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME5_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_rated_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_rated_edit';
$modversion['blocks'][$cptb]['options'] = '10|0';
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_rated.tpl';

/**
 * Random product(s) block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_random.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME6;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME6_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_random_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_random_edit';
$modversion['blocks'][$cptb]['options'] = '1|0|0'; // Nombre de produits, catégorie, produits du mois uniquement ?
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_random.tpl';

/**
 * Products with a discount block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_promotion.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME7;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME7_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_promotion_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_promotion_edit';
$modversion['blocks'][$cptb]['options'] = '10|0';
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_promotion.tpl';

/**
 * Cart block
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_cart.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME8;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME8_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_cart_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_cart_edit';
$modversion['blocks'][$cptb]['options'] = '4'; // Maximum count of items to show
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_cart.tpl';

/**
 * Recommended products
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_recommended.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME9;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME9_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_recomm_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_recomm_edit';
$modversion['blocks'][$cptb]['options'] = '10|0';
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_recommended.tpl';

/**
 * Recently Sold
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_recentlysold.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME10;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME10_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_recentlysold_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_recentlysold_edit';
$modversion['blocks'][$cptb]['options'] = '10'; // Nombre maximum de produits à voir
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_recentlysold.tpl';

/**
 * Last lists
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_recent_lists.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME11;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME11_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_recent_lists_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_recent_lists_edit';
$modversion['blocks'][$cptb]['options'] = '10|0'; // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_recent_lists.tpl';

/**
 * My lists
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_my_lists.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME12;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME12_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_my_lists_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_my_lists_edit';
$modversion['blocks'][$cptb]['options'] = '10'; // Nombre maximum de listes à afficher
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_my_lists.tpl';

/**
 * Lists of the current category ("according to the current category")
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_categoy_lists.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME13;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME13_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_category_lists_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_category_lists_edit';
$modversion['blocks'][$cptb]['options'] = '10|0'; // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_category_lists.tpl';

/**
 * Random lists
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_random_lists.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME14;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME14_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_random_lists_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion_random_lists_edit';
$modversion['blocks'][$cptb]['options'] = '10|0'; // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_random_lists.tpl';

/**
 * Most viewed lists
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_mostviewed_lists.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME15;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME15_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_mostviewed_lists_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion__mostviewed_lists_edit';
$modversion['blocks'][$cptb]['options'] = '10|0'; // Nombre maximum de listes à voir, Type de listes (0 = les 2, 1 = liste cadeaux, 2 = produits recommandés)
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_mostviewed_lists.tpl';

/**
 * Ajax search
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'oledrion_ajax_search.php';
$modversion['blocks'][$cptb]['name'] = _MI_OLEDRION_BNAME16;
$modversion['blocks'][$cptb]['description'] = _MI_OLEDRION_BNAME16_DESC;
$modversion['blocks'][$cptb]['show_func'] = 'b_oledrion_ajax_search_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_oledrion__ajax_search_edit';
$modversion['blocks'][$cptb]['options'] = '1';
$modversion['blocks'][$cptb]['template'] = 'oledrion_block_ajax_search.tpl';

/*
 * $options:
 *					$options[0] - number of tags to display
 *					$options[1] - time duration, in days, 0 for all the time
 *					$options[2] - max font size (px or %)
 *					$options[3] - min font size (px or %)
 */
$modversion['blocks'][] = array(
    'file' => 'oledrion_block_tag.php',
    'name' => _MI_OLEDRION_TAG_CLOUD,
    'description' => 'Show tag cloud',
    'show_func' => 'oledrion_tag_block_cloud_show',
    'edit_func' => 'oledrion_tag_block_cloud_edit',
    'options' => '100|0|150|80',
    'template' => 'oledrion_tag_block_cloud.tpl',
);

/*
 * $options:
 *					$options[0] - number of tags to display
 *					$options[1] - time duration, in days, 0 for all the time
 *					$options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion['blocks'][] = array(
    'file' => 'oledrion_block_tag.php',
    'name' => _MI_OLEDRION_TOP_TAGS,
    'description' => 'Show top tags',
    'show_func' => 'oledrion_tag_block_top_show',
    'edit_func' => 'oledrion_tag_block_top_edit',
    'options' => '50|30|c',
    'template' => 'oledrion_tag_block_top.tpl',
);

// Menu
$modversion['hasMain'] = 1;
$cptm = 0;
require_once 'class/oledrion_utils.php';
if (oledrion_utils::getModuleOption('use_price')) {
    $cptm++;
    $modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME1;
    $modversion['sub'][$cptm]['url'] = 'caddy.php';
}

$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME3;
$modversion['sub'][$cptm]['url'] = 'category.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME4;
$modversion['sub'][$cptm]['url'] = 'categories-map.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME5;
$modversion['sub'][$cptm]['url'] = 'whoswho.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME6;
$modversion['sub'][$cptm]['url'] = 'all-products.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME9;
$modversion['sub'][$cptm]['url'] = 'recommended.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME7;
$modversion['sub'][$cptm]['url'] = 'search.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME8;
$modversion['sub'][$cptm]['url'] = 'cgv.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME10;
$modversion['sub'][$cptm]['url'] = 'my-lists.php';
$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_OLEDRION_SMNAME11;
$modversion['sub'][$cptm]['url'] = 'all-lists.php';

// Ajout des catégories mères en sous menu ********************************************************
global $xoopsModule;
if (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname'] && $xoopsModule->getVar('isactive')) {
    if (!isset($h_oledrion_cat)) {
        $h_oledrion_cat = xoops_getmodulehandler('oledrion_cat', 'oledrion');
    }
    $categories = $h_oledrion_cat->getMotherCategories();
    foreach ($categories as $category) {
        $cptm++;
        $modversion['sub'][$cptm]['name'] = $category->getVar('cat_title');
        $modversion['sub'][$cptm]['url'] = basename($category->getLink());
    }
}

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'oledrion_search';

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'product_id';
$modversion['comments']['pageName'] = 'product.php';

// Comment callback functions
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'oledrion_com_approve';
$modversion['comments']['callback']['update'] = 'oledrion_com_update';

// Templates
$cptt = 0;

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_product_price.tpl';
$modversion['templates'][$cptt]['description'] = "Used in Ajax to display product's price";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_chunk.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_categories_list.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_index.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_category.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_product.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_bill.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_caddy.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_command.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_thankyou.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_cgv.tpl';
$modversion['templates'][$cptt]['description'] = 'General Conditions Of Sale';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_search.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_rss.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_map.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_whoswho.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_allproducts.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_manufacturer.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_rate_product.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_pdf_catalog.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_purchaseorder.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_cancelpurchase.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_recommended.tpl';
$modversion['templates'][$cptt]['description'] = 'Latest recommended products';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_admin_discounts.tpl';
$modversion['templates'][$cptt]['description'] = '';

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_attribute_radio.tpl';
$modversion['templates'][$cptt]['description'] = "Template for attributes of type radio";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_attribute_checkbox.tpl';
$modversion['templates'][$cptt]['description'] = "Template for attributes of type checkbox";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_attribute_select.tpl';
$modversion['templates'][$cptt]['description'] = "Template for attributes of type select (listbox)";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_all_lists.tpl';
$modversion['templates'][$cptt]['description'] = "List of all lists";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_list.tpl';
$modversion['templates'][$cptt]['description'] = "Show a list content";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_mylists.tpl';
$modversion['templates'][$cptt]['description'] = "Enable user to manage his/her lists";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_productsselector.tpl';
$modversion['templates'][$cptt]['description'] = "Used to select products";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_product_box.tpl';
$modversion['templates'][$cptt]['description'] = "Product box";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_product_print.tpl';
$modversion['templates'][$cptt]['description'] = "Product print";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_bill_print.tpl';
$modversion['templates'][$cptt]['description'] = "Bill print";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'oledrion_user.tpl';
$modversion['templates'][$cptt]['description'] = "User page";

// ********************************************************************************************************************
// ****************************************** SETTINGS ****************************************************************
// ********************************************************************************************************************
// Load class
xoops_load('xoopslists');

$cpto = 0;

/**
 * Do you want to use URL rewriting ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'urlrewriting';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_URL_REWR';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Editor to use
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'bl_form_options';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_FORM_OPTIONS';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_FORM_OPTIONS_DESC';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor');
$modversion['config'][$cpto]['default'] = 'dhtmltextarea';

/**
 * Tooltips, or infotips are some small textes you can see when you
 * move your mouse over an article's title. This text contains the
 * first (x) characters of the story
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'infotips';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_INFOTIPS';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_INFOTIPS_DES';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '0';

/**
 * MAX Filesize Upload in kilo bytes
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'maxuploadsize';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_UPLOADFILESIZE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10485760;

/**
 * Do you want to enable your visitors to rate products ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'rateproducts';
$modversion['config'][$cpto]['title'] = "_MI_OLEDRION_RATE";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Global module's Advertisement
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'advertisement';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ADVERTISEMENT';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_ADV_DESCR';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

/**
 * Mime Types
 * Default values : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'mimetypes';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_MIMETYPES';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar";

/**
 * Group of users to which send an email when a product's stock is low (if nothing is typed then there's no alert)
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'stock_alert_email';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_STOCK_EMAIL';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_STOCK_EMAIL_DSC';
$modversion['config'][$cpto]['formtype'] = 'group';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Group of users to wich send an email when a product is sold
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'grp_sold';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_GRP_SOLD';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'group';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Group of users authorized to modify products quantities from the product page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'grp_qty';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_GRP_QTY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'group';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Use RSS Feeds ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'use_rss';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_OPT7';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_OPT7_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Enable PDF Catalog ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'pdf_catalog';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PDF_CATALOG';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Use the price field ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'use_price';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_USE_PRICE';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_USE_PRICE_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Multiply Shipping by product's quantity ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'shipping_quantity';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SHIPPING_QUANTITY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Use tags ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'use_tags';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_USE_TAGS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Use stocks in products attributes ?
 * @since 2.3.2009.03.11
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'attributes_stocks';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_USE_STOCK_ATTRIBUTES';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

// Get Admin groups
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('group_type', 'Admin'));
$member_handler = xoops_gethandler('member');
$admin_xoopsgroups = $member_handler->getGroupList($criteria);
foreach ($admin_xoopsgroups as $key => $admin_group) {
    $admin_groups[$admin_group] = $key;
}
$cpto++;
$modversion['config'][$cpto]['name'] = 'admin_groups';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ADMINGROUPS';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_ADMINGROUPS_DSC';
$modversion['config'][$cpto]['formtype'] = 'select_multi';
$modversion['config'][$cpto]['valuetype'] = 'array';
$modversion['config'][$cpto]['options'] = $admin_groups;
$modversion['config'][$cpto]['default'] = $admin_groups;

$cpto++;
$modversion['config'][$cpto]['name'] = 'admin_groups_part';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ADMINGROUPS_PARTS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'attributes|delivery|gateways|location|manufacturers|packing|property|vendors|categories|discounts|lowstock|newsletter|payment|texts|dashboard|files|lists|maintain|products|vat';

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_META';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

/**
 * Enter meta data manually ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'manual_meta';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_MANUAL_META';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * METAGEN, Max count of keywords to create
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'metagen_maxwords';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_OPT23';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_OPT23_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 40;

/**
 * METAGEN - Keywords order
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'metagen_order';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_OPT24';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 5;
$modversion['config'][$cpto]['options'] = array('_MI_OLEDRION_OPT241' => 0, '_MI_OLEDRION_OPT242' => 1, '_MI_OLEDRION_OPT243' => 2);

/**
 * METAGEN - Black list
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'metagen_blacklist';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_OPT25';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_OPT25_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_MONEY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

/**
 * Money, full label
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'money_full';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_MONEY_F';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_1;

/**
 * Money, short label
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'money_short';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_MONEY_S';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_2;

/**
 * Decimals count
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'decimals_count';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_DECIMAL';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_3;

/**
 * Monnaie's place (left or right) ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'monnaie_place';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CONF00';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_CONF00_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_4;

/**
 * Thousands separator
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thousands_sep';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CONF04';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_5;

/**
 * Decimal separator
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'decimal_sep';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CONF05';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_OLEDRION_SETTING_6;

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_VIEW';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

$cpto++;
$modversion['config'][$cpto]['name'] = 'newproducts';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_NEWLINKS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

$cpto++;
$modversion['config'][$cpto]['name'] = 'perpage';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PERPAGE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

$cpto++;
$modversion['config'][$cpto]['name'] = 'related_limit';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_RELATEDLIMIT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

$cpto++;
$modversion['config'][$cpto]['name'] = 'index_colums';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_COLUMNS_INDEX';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'catagory_colums';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_COLUMNS_CATEGORY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'max_products';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ADAPTED_LIST';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 200;

/**
 * Display a summary table of the last published products (in all categories) ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'summarylast';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SUMMARY1_SHOW';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_SUMMARY1_SHOW_DESC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

/**
 * Display a summary table of the last published products in the same category ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'summarycategory';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SUMMARY2_SHOW';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_SUMMARY2_SHOW_DESC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

/**
 * Better Together ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'better_together';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BEST_TOGETHER';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Display unpublished products ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'show_unpublished';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_UNPUBLISHED';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * If you set this option to yes then you will see two links at the bottom
 * of each item. The first link will enable you to go to the previous
 * item and the other link will bring you to the next item
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'showprevnextlink';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PREVNEX_LINK';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_PREVNEX_LINK_DESC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Display products when there are no more products ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'nostock_display';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_NO_MORE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Message to display when there's not more quantity for a product ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'nostock_msg';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_MSG_NOMORE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

/**
 * Count of visible items in the module's administration
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'items_count';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ITEMSCNT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 30;

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_IMAGE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

/**
 * Do you want to automatically resize the main picture of each product ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'resize_main';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_RESIZE_MAIN';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_RESIZE_MAIN_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Create thumbs automatically ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'create_thumbs';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CREATE_THUMBS';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_CREATE_THUMBS_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Images width
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_width';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_IMAGES_WIDTH';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 400;

/**
 * Images height
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_height';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_IMAGES_HEIGHT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 400;

/**
 * Thumbs width
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thumbs_width';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_THUMBS_WIDTH';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 180;

/**
 * Thumbs height
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thumbs_height';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_THUMBS_HEIGHT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 180;

/**
 * Do you also want to resize categories'pictures to the above dimensions ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'resize_others';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_RESIZE_CATEGORIES';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_SEARCH';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_category';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_CATEGORY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_manufacturers';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_MANUFACTURERS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_vendors';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_VENDORS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_price';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PRICE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_stocks';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_STOCKS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property1';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY1';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property2';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY2';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property3';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY3';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property4';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY4';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property5';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY5';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property6';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY6';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property7';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY7';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property8';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY8';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property9';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY9';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'search_property10';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SEARCH_PROPERTY10';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_CHECKOUT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

/**
 * Use the persistent cart ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'persistent_cart';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PERSISTENT_CART';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_PERSISTENT_CART_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Restrict orders to registred users ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'restrict_orders';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_RESTRICT_ORDERS';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_RESTRICT_ORDERS_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Enable offline payment ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'offline_payment';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_OFFLINE_PAYMENT';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_OFF_PAY_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;


$cpto++;
$modversion['config'][$cpto]['name'] = 'checkout_level';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHECKOUT_LEVEL';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;
$modversion['config'][$cpto]['options'] = array('_MI_OLEDRION_CHECKOUT_LEVEL_1' => 1, '_MI_OLEDRION_CHECKOUT_LEVEL_2' => 2, '_MI_OLEDRION_CHECKOUT_LEVEL_3' => 3);


$cpto++;
$modversion['config'][$cpto]['name'] = 'checkout_country';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHECKOUT_COUNTRY';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_CHECKOUT_COUNTRY_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'checkout_shipping';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHECKOUT_SHIPPING';
$modversion['config'][$cpto]['description'] = '_MI_OLEDRION_CHECKOUT_SHIPPING_DSC';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;
$modversion['config'][$cpto]['options'] = array('_MI_OLEDRION_CHECKOUT_SHIPPING_1' => 1, '_MI_OLEDRION_CHECKOUT_SHIPPING_2' => 2, '_MI_OLEDRION_CHECKOUT_SHIPPING_3' => 3, '_MI_OLEDRION_CHECKOUT_SHIPPING_4' => 4);

/**
 * Ask for VAT number ?
 * @since 2.3.2009.03.09
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'ask_vatnumber';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ASK_VAT_NUMBER';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'ask_bill';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_ASK_BILL';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_SMS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_checkout';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_CHECKOUT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_checkout_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_CHECKOUT_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_validate';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_VALIDATE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_validate_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_VALIDATE_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_pack';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_PACK';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_pack_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_PACK_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_submit';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_SUBMIT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_submit_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_SUBMIT_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_delivery';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_DELIVERY';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_delivery_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_DELIVERY_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_track';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_TRACK';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

$cpto++;
$modversion['config'][$cpto]['name'] = 'sms_track_text';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_SMS_TRACK_TEXT';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

$cpto++;
$modversion['config'][$cpto]['name'] = 'break' . $cpto;
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_BREAK_COMMENT_NOTIFICATION';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'line_break';
$modversion['config'][$cpto]['valuetype'] = 'textbox';
$modversion['config'][$cpto]['default'] = 'head';

// ************************************************************************************************
// ************************* Hidden settings ******************************************************
// ************************************************************************************************
$cpto++;
$modversion['config'][$cpto]['name'] = 'chunk1';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHUNK1';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

$cpto++;
$modversion['config'][$cpto]['name'] = 'chunk2';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHUNK2';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 2;

$cpto++;
$modversion['config'][$cpto]['name'] = 'chunk3';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHUNK3';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 3;

$cpto++;
$modversion['config'][$cpto]['name'] = 'chunk4';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_CHUNK4';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 4;

$cpto++;
$modversion['config'][$cpto]['name'] = 'used_gateway';
$modversion['config'][$cpto]['title'] = "_MI_OLEDRION_GATEWAY";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'paypal';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property1';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY1';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property1_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 1';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property2';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY2';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property2_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 2';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property3';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY3';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property3_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 3';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property4';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY4';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property4_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 4';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property5';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY5';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property5_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 5';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property6';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY6';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property6_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 6';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property7';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY7';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property7_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 7';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property8';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY8';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property8_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 8';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property9';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY9';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property9_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 9';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property10';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY10';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';

$cpto++;
$modversion['config'][$cpto]['name'] = 'product_property10_title';
$modversion['config'][$cpto]['title'] = '_MI_OLEDRION_PRODUCT_PROPERTY_TITLE';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'hidden';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'property 10';

// ************************************************************************************************
// Notifications **********************************************************************************
// ************************************************************************************************
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'oledrion_notify_iteminfo';

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = _MI_OLEDRION_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description'] = _MI_OLEDRION_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('main.php', 'category.php', 'product.php', 'categories-map.php', 'all-products.php');

$modversion['notification']['event'][1]['name'] = 'new_category';
$modversion['notification']['event'][1]['category'] = 'global';
$modversion['notification']['event'][1]['title'] = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFY;
$modversion['notification']['event'][1]['caption'] = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYCAP;
$modversion['notification']['event'][1]['description'] = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][1]['mail_subject'] = _MI_OLEDRION_GLOBAL_NEWCATEGORY_NOTIFYSBJ;

$modversion['notification']['event'][2]['name'] = 'new_product';
$modversion['notification']['event'][2]['category'] = 'global';
$modversion['notification']['event'][2]['title'] = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFY;
$modversion['notification']['event'][2]['caption'] = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYCAP;
$modversion['notification']['event'][2]['description'] = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_newproduct_notify';
$modversion['notification']['event'][2]['mail_subject'] = _MI_OLEDRION_GLOBAL_NEWLINK_NOTIFYSBJ;
