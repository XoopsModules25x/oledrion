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
 * Gestion des commandes
 */
if (!defined("OLEDRION_ADMIN")) exit();
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des commandes
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU5, 4);

        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $filter3 = $totalOrder = 0;
        if (isset($_POST['filter3'])) {
            $filter3 = intval($_POST['filter3']);
        } elseif (isset($_SESSION['filter3'])) {
            $filter3 = intval($_SESSION['filter3']);
        } else {
            $filter3 = 1;
        }
        $_SESSION['filter3'] = $filter3;
        $selected = array('', '', '', '', '', '');
        $conditions = array(OLEDRION_STATE_NOINFORMATION, OLEDRION_STATE_VALIDATED, OLEDRION_STATE_PENDING, OLEDRION_STATE_FAILED, OLEDRION_STATE_CANCELED, OLEDRION_STATE_FRAUD);
        $selected[$filter3] = " selected='selected'";

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('cmd_id', 0, '<>'));
        $criteria->add(new Criteria('cmd_state', $conditions[$filter3], '='));
        $itemsCount = $h_oledrion_commands->getCount($criteria); // Recherche du nombre total de commandes
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=orders');
        }
        $criteria->setSort('cmd_id');
        $criteria->setOrder('DESC');
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $orders = $h_oledrion_commands->getObjects($criteria);
        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        $form = "<form method='post' name='frmfilter' id='frmfilter' action='$baseurl'><b>" . _AM_OLEDRION_LIMIT_TO . "</b> <select name='filter3' id='filter3'><option value='0'" . $selected[0] . ">" . _OLEDRION_CMD_STATE1 . "</option><option value='1'" . $selected[1] . ">" . _OLEDRION_CMD_STATE2 . "</option><option value='2'" . $selected[2] . ">" . _OLEDRION_CMD_STATE3 . "</option><option value='3'" . $selected[3] . ">" . _OLEDRION_CMD_STATE4 . "</option><option value='4'" . $selected[4] . ">" . _OLEDRION_CMD_STATE5 . "</option><option value='5'" . $selected[5] . ">" . _OLEDRION_CMD_STATE6 . "</option></select> <input type='hidden' name='op' id='op' value='orders' /><input type='submit' name='btnfilter' id='btnfilter' value='" . _AM_OLEDRION_FILTER . "' /></form>";
        $confValidateOrder = oledrion_utils::javascriptLinkConfirm(_AM_OLEDRION_CONF_VALIDATE);
        echo "<tr><td colspan='2' align='left'>";
        if (isset($pagenav) && is_object($pagenav)) {
            echo $pagenav->renderNav();
        } else {
            echo '&nbsp;';
        }
        $exportFormats = glob(OLEDRION_PATH . 'admin/exports/*.php');
        $formats = array();
        foreach ($exportFormats as $format) {
            if (strstr($format, 'export.php') === false) {
                $exportName = basename(str_replace('.php', '', $format));
                $formats[] = '<option value="' . $exportName . '">' . $exportName . '</option>';
            }
        }
        echo "</td><td><form method='post' action='$baseurl' name='frmexport' id='frmexport'>" . _AM_OLEDRION_CSV_EXPORT . "<input type='hidden' name='op' id='op' value='orders' /><input type='hidden' name='action' id='action' value='export' /><input type='hidden' name='cmdtype' id='cmdtype' value='$filter3' /><select name='exportfilter' id='exportfilter' size='1'>" . implode("\n", $formats) . "</select> <input type='submit' name='btngoexport' id='btngoexport' value='" . _AM_OLEDRION_OK . "' /></form></td><td align='right' colspan='2'>" . $form . "</td></tr>\n";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_CLIENT . "</th><th align='center'>" . _AM_OLEDRION_TOTAL_SHIPP . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($orders as $item) {
            $id = $item->getVar('cmd_id');
            $class = ($class == 'even') ? 'odd' : 'even';
            $date = formatTimestamp(strtotime($item->getVar('cmd_date')), 's');
            $actions = array();
            $actions[] = "<a target='_blank' href='" . OLEDRION_URL . "invoice.php?id=" . $id . "' title='" . _OLEDRION_DETAILS . "'>" . $icones['details'] . '</a>';
            $actions[] = "<a target='_blank' href='$baseurl?op=orders&action=print&id=" . $id . "' title='" . _OLEDRION_PRINT_VERSION . "'>" . $icones['print'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=validate&id=" . $id . "' " . $confValidateOrder . " title='" . _OLEDRION_VALIDATE_COMMAND . "'>" . $icones['ok'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $date . "</td><td align='center'>" . $item->getVar('cmd_lastname') . ' ' . $item->getVar('cmd_firstname') . "</td><td align='center'>" . $oledrion_Currency->amountForDisplay($item->getVar('cmd_total', 'n')) . ' / ' . $oledrion_Currency->amountForDisplay($item->getVar('cmd_shipping')) . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
            $totalOrder += floatval($item->getVar('cmd_total', 'n'));
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='$class'><td colspan='2' align='center'><b>" . _OLEDRION_TOTAL . "</b></td><td>&nbsp;</td><td align='right'><b>" . $oledrion_Currency->amountForDisplay($totalOrder) . "</b></td><td>&nbsp;</td></tr>";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . "</div>";
        }
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $item = $h_oledrion_commands->get($id);
        if (is_object($item)) {
            xoops_confirm(array('op' => 'orders', 'action' => 'remove', 'id' => $id), 'index.php', _AM_OLEDRION_CONF_DELITEM);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'remove': // Suppression effective d'une commande
        // ****************************************************************************************************************
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item = $h_oledrion_commands->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_commands->removeOrder($item);
            if ($res) {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'validate': // Validation d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item = $h_oledrion_commands->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_commands->validateOrder($item);
            if ($res) {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'export': // Export des commandes au format CSV
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU5, 4);
        $orderType = intval($_POST['cmdtype']);
        $exportFilter = $_POST['exportfilter'];
        $exportFilename = OLEDRION_PATH . 'admin/exports/' . $exportFilter . '.php';
        if (file_exists($exportFilename)) {
            require_once OLEDRION_PATH . 'admin/exports/export.php';
            require_once $exportFilename;
            $className = 'oledrion_' . $exportFilter . '_export';
            if (class_exists($className)) {
                $export = new $className();
                $export->setOrderType($orderType);
                $result = $export->export();
                if ($result === true) {
                    echo "<a href='" . $export->getDownloadUrl() . "'>" . _AM_OLEDRION_EXPORT_READY . '</a>';
                    //echo "<a href='$baseurl?op=orders&action=deleteexport&file=".$export->getDownloadPath()."'>".
                }
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_11);
        }
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;
    // ****************************************************************************************************************
    case 'print': // Print invoice
        // ****************************************************************************************************************
        xoops_cp_header();
        error_reporting(0);
        @$xoopsLogger->activated = false;
        $cmdId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($cmdId == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $order = null;
        $order = $h_oledrion_commands->get($cmdId);
        $caddy = $tmp = $products = $vats = $manufacturers = $tmp2 = $manufacturers = $productsManufacturers = array();

        // Récupération des TVA
        $vats = $h_oledrion_vat->getAllVats(new oledrion_parameters());

        // Récupération des caddy associés
        $caddy = $h_oledrion_caddy->getCaddyFromCommand($cmdId);
        if (count($caddy) == 0) {
            oledrion_utils::redirect(_OLEDRION_ERROR11, 'index.php', 6);
        }

        // Récupération de la liste des produits associés
        foreach ($caddy as $item) {
            $tmp[] = $item->getVar('caddy_product_id');
        }

        // Recherche des produits ***********************************************************************************************
        $products = $h_oledrion_products->getProductsFromIDs($tmp, true);

        // Recherche des fabricants **********************************************************************************************
        $tmp2 = $h_oledrion_productsmanu->getFromProductsIds($tmp);
        $tmp = array();
        foreach ($tmp2 as $item) {
            $tmp[] = $item->getVar('pm_manu_id');
            $productsManufacturers[$item->getVar('pm_product_id')][] = $item;
        }
        $manufacturers = $h_oledrion_manufacturer->getManufacturersFromIds($tmp);
        $handlers = oledrion_handler::getInstance();
        $oledrion_Currency = oledrion_Currency::getInstance();
        // Informations sur la commande ***************************************************************************************
        foreach ($caddy as $itemCaddy) {
            $productForTemplate = $tblJoin = $productManufacturers = $productAttributes = array();
            $product = $products[$itemCaddy->getVar('caddy_product_id')];
            $productForTemplate = $product->toArray(); // Produit
            // Get cat title
            $cat = $h_oledrion_cat->get($productForTemplate['product_cid'])->toArray();
            $productForTemplate['product_cat_title'] = $cat['cat_title'];
            // Est-ce qu'il y a des attributs ?
            if ($handlers->h_oledrion_caddy_attributes->getAttributesCountForCaddy($itemCaddy->getVar('caddy_id')) > 0) {
                $productAttributes = $handlers->h_oledrion_caddy_attributes->getFormatedAttributesForCaddy($itemCaddy->getVar('caddy_id'), $product);
            }
            $productForTemplate['product_attributes'] = $productAttributes;
            $productForTemplate['product_attributes_count'] = count($productAttributes[0]['attribute_options']);

            $productManufacturers = $productsManufacturers[$product->getVar('product_id')];
            foreach ($productManufacturers as $oledrion_productsmanu) {
                if (isset($manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')])) {
                    $manufacturer = $manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')];
                    $tblJoin[] = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                }
            }
            if (count($tblJoin) > 0) {
                $productForTemplate['product_joined_manufacturers'] = implode(', ', $tblJoin);
            }
            $productForTemplate['product_caddy'] = $itemCaddy->toArray();
            if ($productForTemplate['product_final_price_ttc']) {
                $discount = ($productForTemplate['product_caddy']['caddy_qte'] * $productForTemplate['product_final_price_ttc']) - (intval($productForTemplate['product_caddy']['caddy_price']));
                $discount = $discount / $productForTemplate['product_caddy']['caddy_qte'];
            } else {
                $discount = 0;
            }
            $productForTemplate['product_caddy']['caddy_price_t'] = $oledrion_Currency->amountForDisplay($discount);
            $xoopsTpl->append('products', $productForTemplate);
            /*
               echo '<pre>';
               print_r($productForTemplate);
               echo '</pre>';
            */
        }
        $order = $order->toArray();
        $xoopsTpl->assign('order', $order);
        // Call template file
        $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/oledrion/templates/admin/oledrion_order_print.html');
        exit();
        xoops_cp_footer();
        break;
}
