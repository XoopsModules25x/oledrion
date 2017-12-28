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
 * Page d'index, liste des derniers produits
 */

use Xoopsmodules\oledrion;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// require_once OLEDRION_PATH . 'class/Registryfile.php';

// Initialisations
$start     = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$limit     = oledrion\Utility::getModuleOption('newproducts'); // Nombre maximum d'éléments à afficher
$baseurl   = OLEDRION_URL . basename(__FILE__); // URL de ce script (sans son nom)
$registry  = new oledrion\Registryfile();
$lastTitle = '';

// Quelques options pour le template
$xoopsTpl->assign('nostock_msg', oledrion\Utility::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('welcome_msg', nl2br($registry->getfile(OLEDRION_TEXTFILE1)));
$xoopsTpl->assign('columnsCount', oledrion\Utility::getModuleOption('index_colums'));

// Lecture des TVA ********************************************************************************
$vatArray = $vatHandler->getAllVats(new oledrion\Parameters());

// Récupération du nombre total de produits de la base
$xoopsTpl->assign('total_products_count', sprintf(_OLEDRION_THEREARE, $productsHandler->getTotalPublishedProductsCount()));

if ($limit > 0) {
    $itemsCount = $productsHandler->getRecentProductsCount();
    if ($itemsCount > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new \XoopsPageNav($itemsCount, $limit, $start);
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }

    $shelfParameters->resetDefaultValues()->setProductsType('recent')->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title')->setWithXoopsUser(true)->setWithRelatedProducts(true);
    $products = $shelf->getProducts($shelfParameters);

    if (isset($products['lastTitle'])) {
        $lastTitle = strip_tags($products['lastTitle']);
        unset($products['lastTitle']);
    }
    $xoopsTpl->assign('products', $products);
}

// Mise en place des catégories de niveau 1
$count      = 1;
$categories = $categoryHandler->getMotherCategories();
foreach ($categories as $category) {
    $tmp          = $category->toArray();
    $tmp['count'] = $count;
    $xoopsTpl->append('categories', $tmp);
    ++$count;
}

oledrion\Utility::setCSS();
oledrion\Utility::setLocalCSS($xoopsConfig['language']);
oledrion\Utility::setMetas($lastTitle . ' - ' . oledrion\Utility::getModuleName(), oledrion\Utility::getModuleName());
require_once XOOPS_ROOT_PATH . '/footer.php';
