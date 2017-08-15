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
 * Page d'informations sur un fabricant
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_manufacturer.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

// Les tests **************************************************************************************
if (isset($_GET['manu_id'])) {
    $manu_id = (int)$_GET['manu_id'];
} else {
    OledrionUtility::redirect(_OLEDRION_ERROR7, 'index.php', 5);
}
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

// Le fabricant existe ?
$manufacturer = null;
$manufacturer = $h_oledrion_manufacturer->get($manu_id);
if (!is_object($manufacturer)) {
    OledrionUtility::redirect(_OLEDRION_ERROR7, 'index.php', 5);
}

$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', OledrionUtility::getModuleOption('catagory_colums'));
$xoopsTpl->assign('manufacturer', $manufacturer->toArray());
$limit = OledrionUtility::getModuleOption('perpage');

// Lecture des TVA ********************************************************************************
$vatArray = [];
$vatArray = $h_oledrion_vat->getAllVats(new Oledrion_parameters());

// Recherche des produits de ce fabricant *********************************************************
// On commence par chercher le nombre total de ses produits
$itemsCount = $h_oledrion_manufacturer->getManufacturerProductsCount($manu_id);
if ($itemsCount > $limit) {
    $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'manu_id=' . $manu_id);
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

$products = [];
$products = $h_oledrion_manufacturer->getManufacturerProducts($manu_id, $start, $limit);
if (count($products) > 0) {
    $tmp = $categories = [];
    foreach ($products as $product) { // Recherche des catégories
        $tmp[] = $product->getVar('product_cid');
    }
    $tmp = array_unique($tmp);
    sort($tmp);
    if (count($tmp) > 0) {
        $categories = $h_oledrion_cat->getCategoriesFromIds($tmp);
    }
    $cpt   = 1;
    $count = 1;
    foreach ($products as $product) {
        $productForTemplate                     = [];
        $productForTemplate                     = $product->toArray();
        $productForTemplate['count']            = $cpt;
        $productForTemplate['product_category'] = isset($categories[$product->getVar('product_cid')]) ? $categories[$product->getVar('product_cid')]->toArray() : null;
        $productForTemplate['product_count']    = $count;
        $xoopsTpl->append('products', $productForTemplate);
        ++$cpt;
        ++$count;
    }
}

OledrionUtility::setCSS();
OledrionUtility::setLocalCSS($xoopsConfig['language']);
OledrionUtility::loadLanguageFile('modinfo.php');

$xoopsTpl->assign('global_advert', OledrionUtility::getModuleOption('advertisement'));
// By voltan
$breadcrumb = [ /*OLEDRION_URL.'whoswho.php' => _OLEDRION_MANUFACTURERS,*/
                     OLEDRION_URL . basename(__FILE__) => $manufacturer->getVar('manu_name') . ' ' . $manufacturer->getVar('manu_commercialname')
];
$xoopsTpl->assign('breadcrumb', OledrionUtility::breadcrumb($breadcrumb));

//$title = $manufacturer->getVar('manu_name') . ' ' . $manufacturer->getVar('manu_commercialname') . ' - ' . OledrionUtility::getModuleName();

$title = $manufacturer->getVar('manu_name') . ' ' . $manufacturer->getVar('manu_commercialname');
OledrionUtility::setMetas($title, $title, OledrionUtility::createMetaKeywords($manufacturer->getVar('manu_name') . ' ' . $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_bio')));
require_once XOOPS_ROOT_PATH . '/footer.php';
