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
 * Page des catégories
 * Principe :
 * Quand on est sur une catégorie mère (pas de parent) ou si on n'a pas spécifié de catégorie,
 * on affiche (si c'est demandé), les 4 blocs, sinon on affiche les produits de la catégorie
 */
require 'header.php';
$cat_cid = isset($_GET['cat_cid']) ? intval($_GET['cat_cid']) : 0;
$GLOBALS['current_category'] = $cat_cid;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

$category = null;
if ($cat_cid > 0) {
    $category = $h_oledrion_cat->get($cat_cid);
    if (!is_object($category)) {
        oledrion_utils::redirect(_OLEDRION_ERROR8, 'index.php', 5);
    }
}
// On peut afficher les blocs *********************************************************************
$xoopsOption['template_main'] = 'oledrion_category.html';
require_once XOOPS_ROOT_PATH . '/header.php';
$vatArray = $tbl_categories = array();
$limit = oledrion_utils::getModuleOption('perpage');

// Lecture des TVA ********************************************************************************
$vatArray = $h_oledrion_vat->getAllVats(new oledrion_parameters());

// Lecture des catégories *************************************************************************
$categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters());

// Options pour le template ***********************************************************************
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', oledrion_utils::getModuleOption('catagory_colums'));

require_once OLEDRION_PATH . 'class/tree.php';
$tbl_tmp = array();
$mytree = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
$subCategoriesSearched = false;
// Si on est sur une catégorie mère ou si on n'a pas spécifié de catégorie
if ((is_object($category) && $category->getVar('cat_pid') == 0) || $cat_cid == 0) { // On affiche les 4 blocs
    $xoopsTpl->assign('case', 1);

    $tblChildsO = $tblChilds = array();
    if ($cat_cid != 0) {
        $tblChilds[] = $cat_cid;
    }
    if ($cat_cid > 0) {
        $tblChildsO = $mytree->getAllChild($cat_cid);
        foreach ($tblChildsO as $item) {
            $tblChilds[] = $item->getVar('cat_cid');
        }
    }

    if (is_object($category)) { // On est sur une catégorie particulière
        $xoopsTpl->assign('category', $category->toArray());
        $title = _OLEDRION_CATEGORYC . ' ' . $category->getVar('cat_title') . ' - ' . oledrion_utils::getModuleName();
        if (!oledrion_utils::getModuleOption('manual_meta')) {
            oledrion_utils::setMetas($title, $title);
        } else {
            $pageTitle = xoops_trim($category->getVar('cat_metatitle')) == '' ? $title : $category->getVar('cat_metatitle');
            $metaDescription = xoops_trim($category->getVar('cat_metadescription')) != '' ? $category->getVar('cat_metadescription') : $title;
            $metaKeywords = xoops_trim($category->getVar('cat_metakeywords'));
            oledrion_utils::setMetas($pageTitle, $metaDescription, $metaKeywords);
        }
        $xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => $category->getVar('cat_title'))));
        if (OLEDRION_SHOW_SUB_CATEGORIES) {
            $count = 1;
            $firstChilds = array();
            $firstChilds = $mytree->getFirstChild($category->getVar('cat_cid'));
            foreach ($firstChilds as $children) {
                $tmpCategory = array();
                $tmpCategory = $children->toArray();
                $tmpCategory['count'] = $count;
                $xoopsTpl->append('subCategories', $tmpCategory);
                $count++;
            }
            $subCategoriesSearched = true;
        }
    } else { // page d'accueil des catégories
        $title = _OLEDRION_CATEGORIES . ' - ' . oledrion_utils::getModuleName();
        oledrion_utils::setMetas($title, $title);
        $xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_CATEGORIES)));
        if (OLEDRION_SHOW_MAIN_CATEGORIES) {
            $count = 1;
            $motherCategories = $h_oledrion_cat->getMotherCategories();
            foreach ($motherCategories as $mothercategory) {
                $tmpCategory = array();
                $tmpCategory = $mothercategory->toArray();
                $tmpCategory['count'] = $count;
                $xoopsTpl->append('motherCategories', $tmpCategory);
                $count++;
            }
        }
    }

    // Paramétrage des catégories
    $chunk1 = oledrion_utils::getModuleOption('chunk1'); // Produits les plus récents
    $chunk2 = oledrion_utils::getModuleOption('chunk2'); // Produits les plus achetés
    $chunk3 = oledrion_utils::getModuleOption('chunk3'); // Produits les plus vus
    $chunk4 = oledrion_utils::getModuleOption('chunk4'); // Produits les mieux notés

    if ($chunk1 > 0) { // Produits les plus récents (dans cette catégorie ou dans toutes les catégories)
        $products = array();
        $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('recent')->setCategory($tblChilds)->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title');
        $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
        if (count($products) > 0) {
            $xoopsTpl->assign('chunk' . $chunk1 . 'Title', _OLEDRION_MOST_RECENT);
            if (isset($products['lastTitle'])) {
                unset($products['lastTitle']);
            }
            $xoopsTpl->assign('chunk' . $chunk1, $products);
        }
    }

    if ($chunk2 > 0) { // Produits les plus achetés (dans cette catégorie ou dans toutes les catégories)
        $products = array();
        $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('mostsold')->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title')->setCategory($tblChilds);
        $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
        if (count($products) > 0) {
            $xoopsTpl->assign('chunk' . $chunk2 . 'Title', _OLEDRION_MOST_SOLD);
            if (isset($products['lastTitle'])) {
                unset($products['lastTitle']);
            }
            $xoopsTpl->assign('chunk' . $chunk2, $products);
        }
    }

    if ($chunk3 > 0) { // Produits les plus vus
        $products = array();
        $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('mostviewed')->setStart($start)->setLimit($limit)->setSort('product_hits')->setOrder('DESC')->setCategory($tblChilds);
        $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
        if (count($products) > 0) {
            $xoopsTpl->assign('chunk' . $chunk3 . 'Title', _OLEDRION_MOST_VIEWED);
            if (isset($products['lastTitle'])) {
                unset($products['lastTitle']);
            }
            $xoopsTpl->assign('chunk' . $chunk3, $products);
        }
    }

    if ($chunk4 > 0) { // Produits les mieux notés
        $products = array();
        $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('bestrated')->setStart($start)->setLimit($limit)->setSort('product_rating')->setOrder('DESC')->setCategory($tblChilds);
        $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);
        if (count($products) > 0) {
            $xoopsTpl->assign('chunk' . $chunk4 . 'Title', _OLEDRION_MOST_RATED);
            if (isset($products['lastTitle'])) {
                unset($products['lastTitle']);
            }
            $xoopsTpl->assign('chunk' . $chunk4, $products);
        }
    }
}

if (is_object($category) && $cat_cid > 0) { // On est sur une catégorie définie donc on affiche les produits de cette catégorie
    $xoopsTpl->assign('case', 2);
    $xoopsTpl->assign('category', $category->toArray());
    if (OLEDRION_SHOW_SUB_CATEGORIES && !$subCategoriesSearched) {
        $count = 1;
        $firstChilds = array();
        $firstChilds = $mytree->getFirstChild($category->getVar('cat_cid'));
        foreach ($firstChilds as $children) {
            $tmpCategory = array();
            $tmpCategory = $children->toArray();
            $tmpCategory['count'] = $count;
            $xoopsTpl->append('subCategories', $tmpCategory);
            $count++;
        }
    }

    // Pager ******************************************************************************************
    // Recherche du nombre de produits dans cette catégorie
    $productsCount = $h_oledrion_products->getTotalPublishedProductsCount($cat_cid);
    $limit = oledrion_utils::getModuleOption('perpage');
    if ($productsCount > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $catLink = $category->getLink();
        $pagenav = new XoopsPageNav($productsCount, $limit, $start, 'start', 'cat_cid=' . $cat_cid);
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    } else {
        $xoopsTpl->assign('pagenav', '');
    }

    // Breadcrumb *********************************************************************************
    $ancestors = $mytree->getAllParent($cat_cid);
    $ancestors = array_reverse($ancestors);
    $tbl_tmp[] = "<a href='" . OLEDRION_URL . "index.php' title='" . oledrion_utils::makeHrefTitle(oledrion_utils::getModuleName()) . "'>" . oledrion_utils::getModuleName() . '</a>';
    foreach ($ancestors as $item) {
        $tbl_tmp[] = "<a href='" . $item->getLink() . "' title='" . oledrion_utils::makeHrefTitle($item->getVar('cat_title')) . "'>" . $item->getVar('cat_title') . '</a>';
    }
    // Ajout de la catégorie courante
    $tbl_tmp[] = "<a href='" . $category->getLink() . "' title='" . oledrion_utils::makeHrefTitle($category->getVar('cat_title')) . "'>" . $category->getVar('cat_title') . '</a>';
    $breadcrumb = implode(' &raquo; ', $tbl_tmp);
    $xoopsTpl->assign('breadcrumb', $breadcrumb);

    // Meta ***************************************************************************************
    $title = $category->getVar('cat_title');
    if (!oledrion_utils::getModuleOption('manual_meta')) {
        oledrion_utils::setMetas($title, $title, str_replace('&raquo;', ',', $title));
    } else {
        $pageTitle = xoops_trim($category->getVar('cat_metatitle')) == '' ? $title : $category->getVar('cat_metatitle');
        $metaDescription = xoops_trim($category->getVar('cat_metadescription')) != '' ? $category->getVar('cat_metadescription') : $title;
        $metaKeywords = xoops_trim($category->getVar('cat_metakeywords'));
        oledrion_utils::setMetas($pageTitle, $metaDescription, $metaKeywords);
    }


    // Données des Produits *************************************************************************
    $products = array();
    $oledrion_shelf_parameters->resetDefaultValues()->setProductsType('recent')->setCategory($cat_cid)->setStart($start)->setLimit($limit)->setSort('product_id DESC, product_title');
    $products = $oledrion_shelf->getProducts($oledrion_shelf_parameters);

    if (count($products) > 0) {
        if (isset($products['lastTitle'])) {
            unset($products['lastTitle']);
        }
        $xoopsTpl->assign('products', $products);
    }
}

oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);
require_once(XOOPS_ROOT_PATH . '/footer.php');
