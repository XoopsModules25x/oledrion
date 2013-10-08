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
 * Sélecteur de produits
 */
require_once '../../../include/cp_header.php';
require_once '../include/common.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
include_once OLEDRION_PATH . 'class/tree.php';

if (!isset($xoopsUser) || !is_object($xoopsUser)) {
    exit;
}
if (!oledrion_utils::isAdmin()) {
    exit;
}
$xoopsTpl = new XoopsTpl();
$ts = MyTextSanitizer::getInstance();
$limit = oledrion_utils::getModuleOption('items_count'); // Nombre maximum d'éléments à afficher dans l'admin

$oledrion_handlers = oledrion_handler::getInstance();
$searchFields = array('product_title' => _OLEDRION_TITLE,
    'product_summary' => _OLEDRION_SUMMARY,
    'product_description' => _OLEDRION_DESCRIPTION,
    'product_id' => _AM_OLEDRION_ID,
    'product_sku' => _OLEDRION_NUMBER,
    'product_extraid' => _OLEDRION_EXTRA_ID
);
$searchCriterias = array(
    XOOPS_MATCH_START => _STARTSWITH,
    XOOPS_MATCH_END => _ENDSWITH,
    XOOPS_MATCH_EQUAL => _MATCHES,
    XOOPS_MATCH_CONTAIN => _CONTAINS
);

$vendors = array();
$vendors = $oledrion_handlers->h_oledrion_vendors->getList();
$vendors[0] = '---';
sort($vendors);
$categories = $oledrion_handlers->h_oledrion_cat->getAllCategories(new oledrion_parameters());
$mytree = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
$searchVendorSelected = $selectedCategory = $selectedSearchField = 0;

$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$mutipleSelect = isset($_REQUEST['mutipleSelect']) ? intval($_REQUEST['mutipleSelect']) : 0;
$callerName = isset($_REQUEST['callerName']) ? $_REQUEST['callerName'] : '';

if (isset($_REQUEST['op']) && $_REQUEST['op'] == 'search') {
    $searchField = isset($_REQUEST['searchField']) ? $_REQUEST['searchField'] : '';
    $searchCriteria = isset($_REQUEST['searchCriteria']) ? intval($_REQUEST['searchCriteria']) : '';
    $searchText = isset($_REQUEST['searchText']) ? trim($_REQUEST['searchText']) : '';
    $searchVendor = isset($_REQUEST['searchVendor']) ? intval($_REQUEST['searchVendor']) : 0;
    $product_cid = isset($_REQUEST['product_cid']) ? intval($_REQUEST['product_cid']) : 0;

    $selectedSearchField = $searchField;
    $xoopsTpl->assign('searchCriteriaSelected', $searchCriteria);
    $searchVendorSelected = $searchVendor;
    $selectedCategory = $product_cid;
    $additionnalParameters = array();

    $additionnalParameters['op'] = 'search';
    $additionnalParameters['mutipleSelect'] = $mutipleSelect;
    $additionnalParameters['callerName'] = $callerName;
    $additionnalParameters['searchField'] = $searchField;
    $additionnalParameters['searchField'] = $searchField;
    $additionnalParameters['searchCriteria'] = $searchCriteria;
    $additionnalParameters['searchText'] = $searchText;
    $additionnalParameters['searchVendor'] = $searchVendor;
    $additionnalParameters['product_cid'] = $product_cid;

    $criteria = new CriteriaCompo();
    if ($searchText != '') {
        $xoopsTpl->assign('searchTextValue', $ts->htmlSpecialChars($searchText));
        if (array_key_exists($searchField, $searchFields)) {
            switch ($searchCriteria) {
                case XOOPS_MATCH_START:
                    $criteria->add(new Criteria($searchField, $searchText . '%', 'LIKE'));
                    break;
                case XOOPS_MATCH_END:
                    $criteria->add(new Criteria($searchField, '%' . $searchText, 'LIKE'));
                    break;
                case XOOPS_MATCH_EQUAL:
                    $criteria->add(new Criteria($searchField, $searchText, '='));
                    break;
                case XOOPS_MATCH_CONTAIN:
                    $criteria->add(new Criteria($searchField, '%' . $searchText . '%', 'LIKE'));
                    break;
            }
        }
    }

    if ($searchVendor > 0) {
        $criteria->add(new Criteria('product_vendor_id', $searchVendor, '='));
    }
    if ($product_cid > 0) {
        $criteria->add(new Criteria('product_cid', $product_cid, '='));
    }
    $itemsCount = $oledrion_handlers->h_oledrion_products->getcount($criteria);
    $xoopsTpl->assign('productsCount', $itemsCount);
    if ($itemsCount > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', http_build_query($additionnalParameters));
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }
    $criteria->setStart($start);
    $criteria->setLimit($limit);
    $products = array();
    $products = $oledrion_handlers->h_oledrion_products->getObjects($criteria);
    $javascriptSearch = array("'", '"');
    $javascriptReplace = array(' ', ' ');

    if (count($products) > 0) {
        foreach ($products as $product) {
            $productData = $product->toArray();
            $productData['product_title_javascript'] = str_replace($javascriptSearch, $javascriptReplace, $product->getVar('product_title', 'n'));
            //$productData['product_title_javascript'] = $product->getVar('product_title', 'n');
            $xoopsTpl->append('products', $productData);
        }
    }
}

oledrion_utils::loadLanguageFile('modinfo.php');
oledrion_utils::loadLanguageFile('main.php');

$categoriesSelect = $mytree->makeSelBox('product_cid', 'cat_title', '-', $selectedCategory, '---', 0, "class='selectLists'");
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

echo $xoopsTpl->fetch('db:oledrion_productsselector.html');
