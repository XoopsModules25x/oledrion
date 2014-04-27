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
 * @version     $Id: discounts.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion des réductions (dans l'administration)
 */
if (!defined("OLEDRION_ADMIN")) exit();


switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des réductions
        // ****************************************************************************************************************
        xoops_cp_header();
        $form = "<form method='post' action='$baseurl' name='frmadddiscount' id='frmadddiscount'><input type='hidden' name='op' id='op' value='discounts' /><input type='hidden' name='action' id='action' value='add' /><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "' /></form>";
        echo $form;
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU6, 4);

        $discounts = array();
        $itemsCount = 0;
        $class = '';
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;

        $itemsCount = $h_oledrion_discounts->getCount(); // Recherche du nombre total de réductions
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=discounts');
        }

        $criteria = new Criteria('disc_id', 0, '<>');
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $discounts = $h_oledrion_discounts->getObjects($criteria);

        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _TITLE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($discounts as $item) {
            $class = ($class == 'even') ? 'odd' : 'even';
            $id = $item->getVar('disc_id');
            $actions = array();
            $actions[] = "<a href='$baseurl?op=discounts&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=discounts&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            $actions[] = "<a href='$baseurl?op=discounts&action=copy&id=" . $id . "' title='" . _OLEDRION_DUPLICATE_DISCOUNT . "'>" . $icones['copy'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td>" . $id . "</td><td align='center'>" . $item->getVar('disc_title') . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='3' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . "</div>";
        }
        $oledrion_reductions = new oledrion_reductions();

        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'add': // Ajout d'une promotion
    case 'edit': // Edition d'une promo
        // ****************************************************************************************************************

        xoops_cp_header();
        //oledrion_adminMenu(7);
        if ($action == 'edit') {
            $title = _AM_OLEDRION_EDIT_DISCOUNT;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (empty($id)) {
                oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_discounts->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_DSICOUNT;
            $item = $h_oledrion_discounts->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit = false;
        }

        include_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsTpl;
//		$xoopsTpl = new XoopsTpl();
        $xoopsTpl->assign('formTitle', $title);
        $xoopsTpl->assign('action', 'edit');
        $xoopsTpl->assign('baseurl', $baseurl);
        $xoopsTpl->assign('label_submit', $label_submit);
        $discountForTemplate = $item->toArray();
        $discountForTemplate['disc_pediod_checked'] = $item->getVar('disc_date_from') > 0 && $item->getVar('disc_date_to') > 0 ? "checked='checked'" : '';

        $disc_date_from = new XoopsFormTextDateSelect(_AM_OLEDRION_DISCOUNT_PERFROM, 'disc_date_from', 15, $item->getVar('disc_date_from'));
        $discountForTemplate['disc_date_from'] = $disc_date_from->render();
        $disc_date_to = new XoopsFormTextDateSelect(_AM_OLEDRION_DISCOUNT_PERTO, 'disc_date_to', 15, $item->getVar('disc_date_to'));
        $discountForTemplate['disc_date_to'] = $disc_date_to->render();

        $reductionType0 = $reductionType1 = $reductionType2 = '';
        $checked = "checked='checked'";

        switch ($item->getVar('disc_price_type')) {
            case OLEDRION_DISCOUNT_PRICE_TYPE0:
                $reductionType0 = $checked;
                break;
            case OLEDRION_DISCOUNT_PRICE_TYPE1:
                $reductionType1 = $checked;
                break;
            case OLEDRION_DISCOUNT_PRICE_TYPE2:
                $reductionType2 = $checked;
                break;
        }
        $discountForTemplate['disc_price_type_checked0'] = $reductionType0;
        $discountForTemplate['disc_price_type_checked1'] = $reductionType1;
        $discountForTemplate['disc_price_type_checked2'] = $reductionType2;
        // ****
        $disc_price_amount_type1 = $disc_price_amount_type2 = '';
        if ($item->getVar('disc_price_amount_type') == OLEDRION_DISCOUNT_PRICE_REDUCE_PERCENT) {
            $disc_price_amount_type1 = $checked;
        } elseif ($item->getVar('disc_price_amount_type') == OLEDRION_DISCOUNT_PRICE_REDUCE_MONEY) {
            $disc_price_amount_type2 = $checked;
        }
        $discountForTemplate['disc_price_amount_type_checked1'] = $disc_price_amount_type1;
        $discountForTemplate['disc_price_amount_type_checked2'] = $disc_price_amount_type2;
        // ****
        $disc_price_amount_on_checked1 = $disc_price_amount_on_checked2 = '';
        if ($item->getVar('disc_price_amount_on') == OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_PRODUCT) {
            $disc_price_amount_on_checked1 = $checked;
        } elseif ($item->getVar('disc_price_amount_on') == OLEDRION_DISCOUNT_PRICE_AMOUNT_ON_CART) {
            $disc_price_amount_on_checked2 = $checked;
        }
        $discountForTemplate['disc_price_amount_on_checked1'] = $disc_price_amount_on_checked1;
        $discountForTemplate['disc_price_amount_on_checked2'] = $disc_price_amount_on_checked2;
        // ****
        $disc_price_case_checked1 = $disc_price_case_checked2 = $disc_price_case_checked3 = $disc_price_case_checked4 = '';
        switch ($item->getVar('disc_price_case')) {
            case OLEDRION_DISCOUNT_PRICE_CASE_ALL:
                $disc_price_case_checked1 = $checked;
                break;
            case OLEDRION_DISCOUNT_PRICE_CASE_FIRST_BUY:
                $disc_price_case_checked2 = $checked;
                break;
            case OLEDRION_DISCOUNT_PRICE_CASE_PRODUCT_NEVER:
                $disc_price_case_checked3 = $checked;
                break;
            case OLEDRION_DISCOUNT_PRICE_CASE_QTY_IS:
                $disc_price_case_checked4 = $checked;
                break;
        }
        $discountForTemplate['disc_price_case_checked1'] = $disc_price_case_checked1;
        $discountForTemplate['disc_price_case_checked2'] = $disc_price_case_checked2;
        $discountForTemplate['disc_price_case_checked3'] = $disc_price_case_checked3;
        $discountForTemplate['disc_price_case_checked4'] = $disc_price_case_checked4;

        // ****
        $quantityConditions = array(OLEDRION_DISCOUNT_PRICE_QTY_COND1 => OLEDRION_DISCOUNT_PRICE_QTY_COND1_TEXT,
            OLEDRION_DISCOUNT_PRICE_QTY_COND2 => OLEDRION_DISCOUNT_PRICE_QTY_COND2_TEXT,
            OLEDRION_DISCOUNT_PRICE_QTY_COND3 => OLEDRION_DISCOUNT_PRICE_QTY_COND3_TEXT,
            OLEDRION_DISCOUNT_PRICE_QTY_COND4 => OLEDRION_DISCOUNT_PRICE_QTY_COND4_TEXT,
            OLEDRION_DISCOUNT_PRICE_QTY_COND5 => OLEDRION_DISCOUNT_PRICE_QTY_COND5_TEXT
        );
        $xoopsTpl->assign('disc_price_case_qty_cond_options', $quantityConditions);
        $xoopsTpl->assign('disc_price_case_qty_cond_selected', $item->getVar('disc_price_case_qty_cond'));

        // **** Réductions sur les frais de port ****
        $disc_shipping_type_checked1 = $disc_shipping_type_checked2 = $disc_shipping_type_checked3 = $disc_shipping_type_checked4 = '';
        switch ($item->getVar('disc_shipping_type')) {
            case OLEDRION_DISCOUNT_SHIPPING_TYPE1:
                $disc_shipping_type_checked1 = $checked;
                break;
            case OLEDRION_DISCOUNT_SHIPPING_TYPE2:
                $disc_shipping_type_checked2 = $checked;
                break;
            case OLEDRION_DISCOUNT_SHIPPING_TYPE3:
                $disc_shipping_type_checked3 = $checked;
                break;
            case OLEDRION_DISCOUNT_SHIPPING_TYPE4:
                $disc_shipping_type_checked4 = $checked;
                break;
        }
        $discountForTemplate['disc_shipping_type_checked1'] = $disc_shipping_type_checked1;
        $discountForTemplate['disc_shipping_type_checked2'] = $disc_shipping_type_checked2;
        $discountForTemplate['disc_shipping_type_checked3'] = $disc_shipping_type_checked3;
        $discountForTemplate['disc_shipping_type_checked4'] = $disc_shipping_type_checked4;

        // Groupes
        $xoopsTpl->assign('disc_groups_selected', $item->getVar('disc_group'));
        $member_handler = xoops_gethandler('member');
        $groups = array();
        $groups = $member_handler->getGroupList();
        $groups[0] = _ALL;
        ksort($groups);
        $xoopsTpl->assign('disc_groups_options', $groups);

        // Catégories
        $categories = $h_oledrion_cat->getAllCategories(new oledrion_parameters());
        $mytree = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
        $categoriesSelect = $mytree->makeSelBox('disc_cat_cid', 'cat_title', '-', $item->getVar('disc_cat_cid'), _ALL);
        $discountForTemplate['disc_cat_cid_select'] = $categoriesSelect;

        // Fabricants
        $vendors = $h_oledrion_vendors->getList();
        $vendors[0] = _ALL;
        ksort($vendors);

        $xoopsTpl->assign('disc_vendor_id_options', $vendors);
        $xoopsTpl->assign('disc_vendor_id_selected', $item->getVar('disc_vendor_id'));

        // Catégorie
        $xoopsTpl->assign('disc_cat_cid_options', $categoriesSelect);

        // Produits
        $products = $h_oledrion_products->getList();
        $products[0] = _ALL;
        ksort($products);
        $xoopsTpl->assign('disc_product_id_options', $products);
        $xoopsTpl->assign('disc_product_id_selected', $item->getVar('disc_product_id'));

        $productsSelect = $h_oledrion_products->productSelector(new oledrion_parameters(array('caption' => _AM_OLEDRION_DISCOUNT_PRODUCT, 'name' => 'disc_product_id', 'value' => $item->getVar('disc_product_id'), 'size' => 1, 'multiple' => false, 'values' => null, 'showAll' => true, 'sort' => 'product_title', 'order' => 'ASC', 'formName' => 'frmdiscount', 'description' => _AM_OLEDRION_DISCOUNT_HELP1, 'withNull' => _ALL)));
        $xoopsTpl->assign('disc_product_id', $productsSelect->render());

        $xoopsTpl->assign('discount', $discountForTemplate);
        $xoopsTpl->assign('currencyName', oledrion_utils::getModuleOption('money_full'));
        //$editor = oledrion_utils::getWysiwygForm(_AM_OLEDRION_DISCOUNT_DESCR, 'disc_description', $item->getVar('disc_description','e'), 15, 60, 'description_hidden');
        //$xoopsTpl->assign('editor', $editor->render());

        $xoopsTpl->display('db:oledrion_admin_discounts.tpl');
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';


        break;

    // ****************************************************************************************************************
    case 'copy': // Duplication d'une réduction
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'discounts';
        $item = null;
        $item = $h_oledrion_discounts->get($id);
        if (is_object($item)) {
            $newDiscount = $item->xoopsClone();
            if (OLEDRION_DUPLICATED_PLACE == 'right') {
                $newDiscount->setVar('disc_title', xoops_trim($item->getvar('disc_title')) . ' ' . _AM_OLEDRION_DUPLICATED);
            } else {
                $newDiscount->setVar('disc_title', _AM_OLEDRION_DUPLICATED . ' ' . xoops_trim($item->getvar('disc_title')));
            }
            $newDiscount->setVar('disc_id', 0);
            $newDiscount->setNew();
            $res = $h_oledrion_discounts->insert($newDiscount, true);
            if ($res) {
                oledrion_utils::updateCache();
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
    break;

    // ****************************************************************************************************************
    case 'saveedit': // Enregistrement d'une réduction après modification ou ajout
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['disc_id']) ? intval($_POST['disc_id']) : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_discounts->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_discounts->create(true);
        }
        $opRedirect = 'discounts';
        $item->setVars($_POST);
        if (isset($_POST['disc_pediod']) && intval($_POST['disc_pediod']) == 1) {
            $item->setVar('disc_date_from', strtotime($_POST['disc_date_from']));
            $item->setVar('disc_date_to', strtotime($_POST['disc_date_to']));
        } else {
            $item->setVar('disc_date_from', 0);
            $item->setVar('disc_date_to', 0);
        }
        $res = $h_oledrion_discounts->insert($item);
        if ($res) {
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une réduction
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'discounts';
        $item = $h_oledrion_discounts->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_discounts->delete($item, true);
            if ($res) {
                oledrion_utils::updateCache();
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
