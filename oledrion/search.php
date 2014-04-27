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
 * @version     $Id: search.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Recherche dans les produits
 */
require 'header.php';
require_once OLEDRION_PATH . 'class/tree.php';
$GLOBALS['current_category'] = -1; // Pour le bloc des catégories
$xoopsOption['template_main'] = 'oledrion_search.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$limit = oledrion_utils::getModuleOption('newproducts'); // Nombre maximum d'éléments à afficher
$categories = $manufacturers = $vendors = array();
$baseurl = OLEDRION_URL . basename(__FILE__); // URL de ce script (sans son nom)

$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('columnsCount', oledrion_utils::getModuleOption('catagory_colums'));

$categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters());
$vendors = $h_oledrion_vendors->getAllVendors(new oledrion_parameters());
$manufacturers = $h_oledrion_manufacturer->getItems(0, 0, 'manu_name', 'ASC', false);


if ((isset($_POST['op']) && $_POST['op'] == 'go') || isset($_GET['start'])) { // Recherche des résultats
    $xoopsTpl->assign('search_results', true);
    $xoopsTpl->assign('global_advert', oledrion_utils::getModuleOption('advertisement'));
    $xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_SEARCHRESULTS)));
    oledrion_utils::setMetas(oledrion_utils::getModuleName() . ' - ' . _OLEDRION_SEARCHRESULTS, oledrion_utils::getModuleName() . ' - ' . _OLEDRION_SEARCHRESULTS);

    if (!isset($_GET['start'])) {
        $sql = 'SELECT b.product_id, b.product_title, b.product_submitted, b.product_submitter, b.product_thumb_url, b.product_price, b.product_property1, b.product_property2, b.product_property3, b.product_property4, b.product_property5, b.product_property6, b.product_property7, b.product_property8, b.product_property9, b.product_property10, b.product_stock, b.product_summary FROM ' . $xoopsDB->prefix('oledrion_products') . ' b, ' . $xoopsDB->prefix('oledrion_productsmanu') . ' a WHERE (b.product_id = a.pm_product_id AND b.product_online = 1 ';
        if (oledrion_utils::getModuleOption('show_unpublished') == 0) { // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND b.product_submitted <= ' . time();
        }
        if (oledrion_utils::getModuleOption('nostock_display') == 0) { // Se limiter aux seuls produits encore en stock
            $sql .= ' AND b.product_stock > 0';
        }
        $sql .= ') ';

        // Recherche sur une catégorie
        if (isset($_POST['product_category'])) {
            $cat_cid = intval($_POST['product_category']);
            if ($cat_cid > 0) {
                $sql .= 'AND (b.product_cid = ' . $cat_cid . ')';
            }
        }

        // Recherche sur les fabricants
        if (isset($_POST['product_manufacturers'])) {
            $submittedManufacturers = null;
            $submittedManufacturers = $_POST['product_manufacturers'];
            if (is_array($submittedManufacturers) && intval($submittedManufacturers[0]) == 0) {
                $submittedManufacturers = array_shift($submittedManufacturers);
            }
            if (is_array($submittedManufacturers) && count($submittedManufacturers) > 0) {
                array_walk($submittedManufacturers, 'intval');
                $sql .= ' AND (a.pm_manu_id IN ( ' . implode(',', $submittedManufacturers) . '))';
            } else {
                $submittedManufacturer = intval($submittedManufacturers);
                if ($submittedManufacturer > 0) {
                    $sql .= ' AND (a.pm_manu_id = ' . $submittedManufacturer . ')';
                }
            }
        }

        // Recherche sur les vendeurs
        if (isset($_POST['product_vendors'])) {
            $vendor = intval($_POST['product_vendors']);
            if ($vendor > 0) {
                $sql .= ' AND (product_vendor_id = ' . $vendor . ')';
            }
        }

        // set from
        if (isset($_POST['product_from'])) {
            $product_from = intval($_POST['product_from']);
            if ($product_from > 0) {
                $sql .= ' AND (product_price > ' . $product_from . ')';
            }
        }

        // set to
        if (isset($_POST['product_to'])) {
            $product_to = intval($_POST['product_to']);
            if ($product_to > 0) {
                $sql .= ' AND (product_price < ' . $product_to . ')';
            }
        }

        if ($_POST['product_stock'] == 2) {
            $sql .= ' AND (product_stock > 0)';
        } elseif ($_POST['product_stock'] == 0) {
            $sql .= ' AND (product_stock = 0)';
        }

        if (isset($_POST['product_property1'])) {
            if ($_POST['product_property1']) {
                $sql .= ' AND (b.product_property1 = "' . $_POST['product_property1'] . '")';
            }
        }

        if (isset($_POST['product_property2'])) {
            if ($_POST['product_property2']) {
                $sql .= ' AND (b.product_property2 = "' . $_POST['product_property2'] . '")';
            }
        }

        if (isset($_POST['product_property3'])) {
            if ($_POST['product_property3']) {
                $sql .= ' AND (b.product_property3 = "' . $_POST['product_property3'] . '")';
            }
        }

        if (isset($_POST['product_property4'])) {
            if ($_POST['product_property4']) {
                $sql .= ' AND (b.product_property4 = "' . $_POST['product_property4'] . '")';
            }
        }

        if (isset($_POST['product_property5'])) {
            if ($_POST['product_property5']) {
                $sql .= ' AND (b.product_property5 = "' . $_POST['product_property5'] . '")';
            }
        }

        if (isset($_POST['product_property6'])) {
            if ($_POST['product_property6']) {
                $sql .= ' AND (b.product_property6 = "' . $_POST['product_property6'] . '")';
            }
        }

        if (isset($_POST['product_property7'])) {
            if ($_POST['product_property7']) {
                $sql .= ' AND (b.product_property7 = "' . $_POST['product_property7'] . '")';
            }
        }

        if (isset($_POST['product_property8'])) {
            if ($_POST['product_property8']) {
                $sql .= ' AND (b.product_property8 = "' . $_POST['product_property8'] . '")';
            }
        }

        if (isset($_POST['product_property9'])) {
            if ($_POST['product_property9']) {
                $sql .= ' AND (b.product_property9 = "' . $_POST['product_property9'] . '")';
            }
        }

        if (isset($_POST['product_property10'])) {
            if ($_POST['product_property10']) {
                $sql .= ' AND (b.product_property10 = "' . $_POST['product_property10'] . '")';
            }
        }

        // Recherche sur du texte
        if (isset($_POST['product_text']) && xoops_trim($_POST['product_text']) != '') {
            $temp_queries = $queries = array();
            $temp_queries = preg_split('/[\s,]+/', $_POST['product_text']);

            foreach ($temp_queries as $q) {
                $q = trim($q);
                $queries[] = $myts->addSlashes($q);
            }
            if (count($queries) > 0) {
                $tmpObject = new oledrion_products();
                $datas = $tmpObject->getVars();
                $fields = array();
                $cnt = 0;
                foreach ($datas as $key => $value) {
                    if ($value['data_type'] == XOBJ_DTYPE_TXTBOX || $value['data_type'] == XOBJ_DTYPE_TXTAREA) {
                        if ($cnt == 0) {
                            $fields[] = 'b.' . $key;
                        } else {
                            $fields[] = ' OR b.' . $key;
                        }
                        $cnt++;
                    }
                }
                $count = count($queries);
                $cnt = 0;
                $sql .= ' AND ';
                $searchType = intval($_POST['search_type']);
                $andor = ' OR ';
                foreach ($queries as $oneQuery) {
                    $sql .= '(';
                    switch ($searchType) {
                        case 0: // Commence par
                            $cond = " LIKE '" . $oneQuery . "%' ";
                            break;
                        case 1: // Finit par
                            $cond = " LIKE '%" . $oneQuery . "' ";
                            break;
                        case 2: // Correspond à
                            $cond = " = '" . $oneQuery . "' ";
                            break;
                        case 3: // Contient
                            $cond = " LIKE '%" . $oneQuery . "%' ";
                            break;
                    }
                    $sql .= implode($cond, $fields) . $cond . ')';
                    $cnt++;
                    if ($cnt != $count) {
                        $sql .= ' ' . $andor . ' ';
                    }
                }
            }
        }
        $_SESSION['criteria_oledrion'] = serialize($sql);
    } else { // $_GET['start'] est en place, on a cliqué sur un chevron pour aller voir les autres pages, il faut travailler à partir des informations de la session
        if (isset($_SESSION['criteria_oledrion'])) {
            $sql = unserialize($_SESSION['criteria_oledrion']);
        }
    }
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $sqlCount = str_replace("b.product_id, b.product_title, b.product_submitted, b.product_submitter", "Count(*) as cpt", $sql);
    $result = $xoopsDB->query($sqlCount);
    $rowCount = $xoopsDB->fetchArray($result);
    if ($rowCount['cpt'] > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new XoopsPageNav($rowCount['cpt'], $limit, $start, 'start');
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }

    $sql .= ' GROUP BY b.product_id ORDER BY product_submitted DESC';
    $result = $xoopsDB->query($sql, $limit, $start);
    $ret = array();
    $tempProduct = $h_oledrion_products->create(true);
    $count = 1;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret = array();
        $ret['product_url_rewrited'] = $tempProduct->getLink($myrow['product_id'], $myrow['product_title']);
        $ret['product_title'] = $myts->htmlSpecialChars($myrow['product_title']);
        $ret['product_href_title'] = oledrion_utils::makeHrefTitle($myts->htmlSpecialChars($myrow['product_title']));
        $ret['product_time'] = $myrow['product_submitted'];
        $ret['product_uid'] = $myrow['product_submitter'];
        $ret['product_id'] = $myrow['product_id'];
        $ret['product_thumb_url'] = $myrow['product_thumb_url'];
        $ret['product_thumb_full_url'] = OLEDRION_PICTURES_URL . '/' . $myrow['product_thumb_url'];
        $ret['product_property1'] = $myrow['product_property1'];
        $ret['product_property2'] = $myrow['product_property2'];
        $ret['product_property3'] = $myrow['product_property3'];
        $ret['product_property4'] = $myrow['product_property4'];
        $ret['product_property5'] = $myrow['product_property5'];
        $ret['product_property6'] = $myrow['product_property6'];
        $ret['product_property7'] = $myrow['product_property7'];
        $ret['product_property8'] = $myrow['product_property8'];
        $ret['product_property9'] = $myrow['product_property9'];
        $ret['product_property10'] = $myrow['product_property10'];
        $ret['product_price'] = $myrow['product_price'];
        if ($myrow['product_price'] == 0) {
            $criteria = new CriteriaCompo ();
            $criteria->add(new Criteria('attribute_product_id', $myrow['product_id']));
            $attribute = oledrion_handler::getInstance()->h_oledrion_attributes->getObjects($criteria, false);
            foreach ($attribute as $root) {
                $ret['product_price'] = $root->getVar('attribute_default_value');
            }
        }
        $ret['product_stock'] = $myrow['product_stock'];
        $ret['product_price_ttc'] = oledrion_utils::getTTC($ret['product_price'], '');
        $ret['product_count'] = $count;
        $ret['product_summary'] = $myrow['product_summary'];
        $xoopsTpl->append('products', $ret);
        $count++;
    }
    unset($tempProduct);
} else {
    $xoopsTpl->assign('search_results', false);
    $xoopsTpl->assign('global_advert', oledrion_utils::getModuleOption('advertisement'));
    $xoopsTpl->assign('breadcrumb', oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_SEARCHFOR)));
    oledrion_utils::setMetas(oledrion_utils::getModuleName() . ' - ' . _OLEDRION_SEARCHFOR, oledrion_utils::getModuleName() . ' - ' . _OLEDRION_SEARCHFOR);
}

require_once OLEDRION_PATH . 'include/product_search_form.php';
$sform = oledrion_utils::formMarkRequiredFields($sform);
$xoopsTpl->assign('search_form', $sform->render());

oledrion_utils::setCSS();
oledrion_utils::setLocalCSS($xoopsConfig['language']);

require_once XOOPS_ROOT_PATH . '/footer.php';
