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
 * Page d'index, liste des derniers produits
 */
require 'header.php';
$GLOBALS['current_category'] = -1;
$xoopsOption['template_main'] = 'oledrion_index.html';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once OLEDRION_PATH . 'class/registryfile.php';

// Initialisations
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = oledrion_utils::getModuleOption('newproducts'); // Nombre maximum d'éléments à afficher
$baseurl = OLEDRION_URL . basename(__FILE__); // URL de ce script (sans son nom)
$registry = new oledrion_registryfile();

// Quelques options pour le template
$xoopsTpl->assign('nostock_msg', oledrion_utils::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('welcome_msg', nl2br($registry->getfile(OLEDRION_TEXTFILE1)));
$xoopsTpl->assign('columnsCount', oledrion_utils::getModuleOption('index_colums'));

// Lecture des TVA ********************************************************************************
$vatArray = $h_oledrion_vat->getAllVats(new oledrion_parameters());

// Récupération du nombre total de produits de la base
$xoopsTpl->assign('total_products_count', sprintf(_OLEDRION_THEREARE, $h_oledrion_products->getTotalPublishedProductsCount()));

if ($limit > 0) {
    $itemsCount = $h_oledrion_products->getRecentProductsCount();
    if ($itemsCount > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new XoopsPageNav($itemsCount, $limit, $start);
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }

    $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('recent')->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title')->setWithXoopsUser(true)->setWithRelatedProducts(true);
    $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);

    if (isset($products['lastTitle'])) {
        $lastTitle = strip_tags($products['lastTitle']);
        unset($products['lastTitle']);
    }
    $xoopsTpl->assign('products', $products);
}

// Mise en place des catégories de niveau 1
$count = 1;
$categories = $h_oledrion_cat->getMotherCategories();
foreach ($categories as $category) {
    $tmp = $category->toArray();
    $tmp['count'] = $count;
    $xoopsTpl->append('categories', $tmp);
    $count++;
}

oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
oledrion_utils::setMetas($lastTitle . ' - ' . oledrion_utils::getModuleName(), oledrion_utils::getModuleName());
require_once(XOOPS_ROOT_PATH . '/footer.php');
