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

/**
 * Liste de tous les produits du catalogue (en fonction des paramètres du module)
 */
require 'header.php';
$GLOBALS['current_category'] = -1;
$xoopsOption['template_main'] = 'oledrion_allproducts.html';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$categories = $vatArray = array();

// Lecture des TVA
$vatArray = $h_oledrion_vat->getAllVats(new oledrion_parameters());
// Préférences du module
$xoopsTpl->assign('mod_pref', $mod_pref);

$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = oledrion_utils::getModuleOption('perpage');

// Lecture des produits
$itemsCount = $oledrion_shelf->getProductsCount('recent');
if ($itemsCount > $limit) {
    $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

$products = array();
$oledrion_shelf_parameters->resetDefaultValues()->setProductsType('recent')->setStart($start)->setLimit($limit)->setSort('product_submitted DESC, product_title');
$products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
if (isset($products['lastTitle'])) {
    $lastTitle = strip_tags($products['lastTitle']);
    unset($products['lastTitle']);
}
$xoopsTpl->assign('products', $products);

$xoopsTpl->assign('pdf_catalog', oledrion_utils::getModuleOption('pdf_catalog'));

oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
if (!OLEDRION_MY_THEME_USES_JQUERY) {
    $xoTheme->addScript("browse.php?Frameworks/jquery/jquery.js");
}
oledrion_utils::callJavascriptFile('noconflict.js');
oledrion_utils::callJavascriptFile('tablesorter/jquery.tablesorter.min.js');

oledrion_utils::loadLanguageFile('modinfo.php');

$xoopsTpl->assign('global_advert', oledrion_utils::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME6)));

$title = _MI_OLEDRION_SMNAME6 . ' - ' . oledrion_utils::getModuleName();
oledrion_utils::setMetas($title, $title);
require_once(XOOPS_ROOT_PATH . '/footer.php');
