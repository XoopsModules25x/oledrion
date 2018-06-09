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

use XoopsModules\Oledrion;

/**
 * Sélecteur de produits
 */
require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once dirname(__DIR__) . '/include/common.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
// require_once OLEDRION_PATH . 'class/tree.php';

if (!isset($xoopsUser) || !is_object($xoopsUser)) {
    exit;
}
if (!Oledrion\Utility::isAdmin()) {
    exit;
}
$xoopsTpl = new \XoopsTpl();
$ts       = \MyTextSanitizer::getInstance();
$limit    = Oledrion\Utility::getModuleOption('items_count'); // Nombre maximum d'éléments à afficher dans l'admin

//$oledrionHandlers = HandlerManager::getInstance();
$searchFields    = [
    'product_title'       => _OLEDRION_TITLE,
    'product_summary'     => _OLEDRION_SUMMARY,
    'product_description' => _OLEDRION_DESCRIPTION,
    'product_id'          => _AM_OLEDRION_ID,
    'product_sku'         => _OLEDRION_NUMBER,
    'product_extraid'     => _OLEDRION_EXTRA_ID
];
$searchCriterias = [
    XOOPS_MATCH_START   => _STARTSWITH,
    XOOPS_MATCH_END     => _ENDSWITH,
    XOOPS_MATCH_EQUAL   => _MATCHES,
    XOOPS_MATCH_CONTAIN => _CONTAINS
];

$db              = \XoopsDatabaseFactory::getDatabaseConnection();
$vendorsHandler  = new Oledrion\VendorsHandler($db);
$categoryHandler = new Oledrion\CategoryHandler($db);
$vendors         = [];
$vendors         = $vendorsHandler->getList();
$vendors[0]      = '---';
sort($vendors);
//$categories           = $oledrionHandlers->h_oledrion_cat->getAllCategories(new Oledrion\Parameters());
$categories           = $categoryHandler->getAllCategories(new Oledrion\Parameters());
$mytree               = new Oledrion\XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
$searchVendorSelected = $selectedCategory = $selectedSearchField = 0;

$start         = \Xmf\Request::getInt('start', 0, 'REQUEST');
$mutipleSelect = \Xmf\Request::getInt('mutipleSelect', 0, 'REQUEST');
$callerName    = \Xmf\Request::getString('callerName', '', 'REQUEST');

if (isset($_REQUEST['op']) && 'search' === $_REQUEST['op']) {
    $searchField    = \Xmf\Request::getString('searchField', '', 'REQUEST');
    $searchCriteria = \Xmf\Request::getInt('searchCriteria', '', 'REQUEST');
    $searchText     = isset($_REQUEST['searchText']) ? trim($_REQUEST['searchText']) : '';
    $searchVendor   = \Xmf\Request::getInt('searchVendor', 0, 'REQUEST');
    $product_cid    = \Xmf\Request::getInt('product_cid', 0, 'REQUEST');

    $selectedSearchField = $searchField;
    $xoopsTpl->assign('searchCriteriaSelected', $searchCriteria);
    $searchVendorSelected  = $searchVendor;
    $selectedCategory      = $product_cid;
    $additionnalParameters = [];

    $additionnalParameters['op']             = 'search';
    $additionnalParameters['mutipleSelect']  = $mutipleSelect;
    $additionnalParameters['callerName']     = $callerName;
    $additionnalParameters['searchField']    = $searchField;
    $additionnalParameters['searchField']    = $searchField;
    $additionnalParameters['searchCriteria'] = $searchCriteria;
    $additionnalParameters['searchText']     = $searchText;
    $additionnalParameters['searchVendor']   = $searchVendor;
    $additionnalParameters['product_cid']    = $product_cid;

    $criteria = new \CriteriaCompo();
    if ('' !== $searchText) {
        $xoopsTpl->assign('searchTextValue', $ts->htmlSpecialChars($searchText));
        if (array_key_exists($searchField, $searchFields)) {
            switch ($searchCriteria) {
                case XOOPS_MATCH_START:
                    $criteria->add(new \Criteria($searchField, $searchText . '%', 'LIKE'));
                    break;
                case XOOPS_MATCH_END:
                    $criteria->add(new \Criteria($searchField, '%' . $searchText, 'LIKE'));
                    break;
                case XOOPS_MATCH_EQUAL:
                    $criteria->add(new \Criteria($searchField, $searchText, '='));
                    break;
                case XOOPS_MATCH_CONTAIN:
                    $criteria->add(new \Criteria($searchField, '%' . $searchText . '%', 'LIKE'));
                    break;
            }
        }
    }

    if ($searchVendor > 0) {
        $criteria->add(new \Criteria('product_vendor_id', $searchVendor, '='));
    }
    if ($product_cid > 0) {
        $criteria->add(new \Criteria('product_cid', $product_cid, '='));
    }
    $itemsCount = $productsHandler->getCount($criteria);
    $xoopsTpl->assign('productsCount', $itemsCount);
    if ($itemsCount > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new \XoopsPageNav($itemsCount, $limit, $start, 'start', http_build_query($additionnalParameters));
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }
    $criteria->setStart($start);
    $criteria->setLimit($limit);
    $products          = [];
    $products          = $productsHandler->getObjects($criteria);
    $javascriptSearch  = ["'", '"'];
    $javascriptReplace = [' ', ' '];

    if (count($products) > 0) {
        foreach ($products as $product) {
            $productData                             = $product->toArray();
            $productData['product_title_javascript'] = str_replace($javascriptSearch, $javascriptReplace, $product->getVar('product_title', 'n'));
            //$productData['product_title_javascript'] = $product->getVar('product_title', 'n');
            $xoopsTpl->append('products', $productData);
        }
    }
}

$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

if (Oledrion\Utility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
    $categoriesSelect = $mytree->makeSelectElement('product_cid', 'cat_title', '-', $selectedCategory, true, 0, '', '');
    $xoopsTpl->assign('searchCategory', $categoriesSelect->render());
} else {
    $categoriesSelect = $mytree->makeSelBox('product_cid', 'cat_title', '-', $selectedCategory, '---', 0, "class='selectLists'");
    $xoopsTpl->assign('searchCategory', $categoriesSelect);
}
$xoopsTpl->assign('callerName', $callerName);
$xoopsTpl->assign('sart', $start);
$xoopsTpl->assign('theme_set', xoops_getcss($xoopsConfig['theme_set']));
$xoopsTpl->assign('xoopsConfig', $xoopsConfig);
$xoopsTpl->assign('mutipleSelect', $mutipleSelect);
$xoopsTpl->assign('searchVendorSelected', $searchVendorSelected);
$xoopsTpl->assign('baseurl', OLEDRION_URL . 'admin/' . basename(__FILE__)); // URL de ce script
$xoopsTpl->assign('searchVendor', $vendors);
$xoopsTpl->assign('searchCriteria', $searchCriterias);
$xoopsTpl->assign('searchField', $searchFields);
$xoopsTpl->assign('searchCategory', $categoriesSelect);
$xoopsTpl->assign('searchFieldSelected', $selectedSearchField);

echo $xoopsTpl->fetch('db:oledrion_productsselector.tpl');
