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
 * Affiche la liste des produits recommandés
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_recommended.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// require_once OLEDRION_PATH . 'class/Registryfile.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

// Initialisations
$tbl_products         = $tbl_categories = $tbl_vendors = $tbl_users = $tbl_tmp_user = $tbl_tmp_categ = $tbl_tmp_lang = $tbl_tmp_vat = $tbl_vat = [];
$tbl_products_id      = $tbl_auteurs = $tbl_infos_auteurs = $tbl_tmp_auteurs = [];
$tbl_tmp_related      = $tbl_related = $tbl_info_related_products = [];
$tbl_related_products = [];
$start                = \Xmf\Request::getInt('start', 0, 'GET');
$limit                = Oledrion\Utility::getModuleOption('perpage');
$baseurl              = OLEDRION_URL . basename(__FILE__); // URL de ce script (sans son nom)
$oledrion_Currency    = Oledrion\Currency::getInstance();

$registry = new Oledrion\Registryfile();

// Lecture des TVA ********************************************************************************
$vatArray = [];
$vatArray = $vatHandler->getAllVats(new Oledrion\Parameters());

// Quelques options pour le template
$xoopsTpl->assign('nostock_msg', Oledrion\Utility::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', Oledrion\Utility::getModuleOption('category_colums'));
$xoopsTpl->assign('welcome_msg', nl2br($registry->getfile(OLEDRION_TEXTFILE3)));

// Récupération du nombre total de produits publiés dans la base
$itemsCount = $productsHandler->getRecommendedCount();
if ($itemsCount > $limit) {
    $pagenav = new \XoopsPageNav($itemsCount, $limit, $start);
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

if ($limit > 0) {
    // Récupération de la liste des produits récents
    $shelfParameters->resetDefaultValues()->setProductsType('recommended')->setStart($start)->setLimit($limit)->setSort('product_recommended')->setOrder('DESC')->setCategory(0)->setWithXoopsUser(true)->setWithRelatedProducts(true);
    $products = $shelf->getProducts($shelfParameters);
    if (isset($products['lastTitle'])) {
        $lastTitle = strip_tags($products['lastTitle']);
        unset($products['lastTitle']);
    }
    $xoopsTpl->assign('products', $products);
}
$xoopsTpl->assign('global_advert', Oledrion\Utility::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_RECOMMENDED]));

Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
Oledrion\Utility::setMetas(_OLEDRION_RECOMMENDED . ' - ' . Oledrion\Utility::getModuleName(), Oledrion\Utility::getModuleName());
require_once XOOPS_ROOT_PATH . '/footer.php';
