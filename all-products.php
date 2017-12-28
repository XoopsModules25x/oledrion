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

use Xoopsmodules\oledrion;

/**
 * Liste de tous les produits du catalogue (en fonction des paramètres du module)
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_allproducts.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$categories = $vatArray = [];
$db         = \XoopsDatabaseFactory::getDatabaseConnection();
$vatHandler = new oledrion\VatHandler($db);

// Lecture des TVA
$vatArray = $vatHandler->getAllVats(new oledrion\Parameters());
// Préférences du module
$xoopsTpl->assign('mod_pref', $mod_pref);

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$limit = oledrion\Utility::getModuleOption('perpage');

// Lecture des produits
$itemsCount = $shelf->getProductsCount('recent');
if ($itemsCount > $limit) {
    $pagenav = new \XoopsPageNav($itemsCount, $limit, $start, 'start');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

$products = [];
$shelfParameters->resetDefaultValues()->setProductsType('recent')->setStart($start)->setLimit($limit)->setSort('product_submitted DESC, product_title');
$products = $shelf->getProducts($shelfParameters);
if (isset($products['lastTitle'])) {
    $lastTitle = strip_tags($products['lastTitle']);
    unset($products['lastTitle']);
}
$xoopsTpl->assign('products', $products);

$xoopsTpl->assign('pdf_catalog', oledrion\Utility::getModuleOption('pdf_catalog'));

oledrion\Utility::setCSS();
oledrion\Utility::setLocalCSS($xoopsConfig['language']);
if (!OLEDRION_MY_THEME_USES_JQUERY) {
    $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
}
oledrion\Utility::callJavascriptFile('noconflict.js');
oledrion\Utility::callJavascriptFile('tablesorter/jquery.tablesorter.min.js');

$helper->loadLanguage('modinfo');

$xoopsTpl->assign('global_advert', oledrion\Utility::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME6]));

$title = _MI_OLEDRION_SMNAME6 . ' - ' . oledrion\Utility::getModuleName();
oledrion\Utility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
