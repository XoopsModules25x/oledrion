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
use XoopsModules\Oledrion\Constants;

/**
 * Gestion des commandes
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des commandes
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=orders');
        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU5, 4);

        $start   = \Xmf\Request::getInt('start', 0, 'GET');
        $totalOrder = 0;
        $filter3 = 1;
        if (\Xmf\Request::hasVar('filter3', 'POST')) {
            $filter3 = \Xmf\Request::getInt('filter3', 0, 'POST');
        } elseif (\Xmf\Request::hasVar('filter3', 'SESSION')) {
            $filter3 = \Xmf\Request::getInt('filter3', 0, 'SESSION');
        }
        $_SESSION['filter3'] = $filter3;
        $selected            = ['', '', '', '', '', '', '', '', ''];
        $conditions          = [
            Constants::OLEDRION_STATE_NOINFORMATION,
            Constants::OLEDRION_STATE_VALIDATED,
            Constants::OLEDRION_STATE_PENDING,
            Constants::OLEDRION_STATE_FAILED,
            Constants::OLEDRION_STATE_CANCELED,
            Constants::OLEDRION_STATE_FRAUD,
            Constants::OLEDRION_STATE_PACKED,
            Constants::OLEDRION_STATE_SUBMITED,
            Constants::OLEDRION_STATE_DELIVERED
        ];
        $selected[$filter3]  = ' selected';

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('cmd_id', 0, '<>'));
        $criteria->add(new \Criteria('cmd_state', $conditions[$filter3], '='));
        $itemsCount = $commandsHandler->getCount($criteria); // Recherche du nombre total de commandes
        if ($itemsCount > $limit) {
            $pagenav = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=orders');
        }
        $criteria->setSort('cmd_id');
        $criteria->setOrder('DESC');
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $orders = $commandsHandler->getObjects($criteria);
        $class  = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        $form              = "<form method='post' name='frmfilter' id='frmfilter' action='$baseurl'><b>" . _AM_OLEDRION_LIMIT_TO . "</b> <select name='filter3' id='filter3'>
        <option value='0'" . $selected[0] . '>' . _OLEDRION_CMD_STATE1 . "</option>
        <option value='1'" . $selected[1] . '>' . _OLEDRION_CMD_STATE2 . "</option>
        <option value='2'" . $selected[2] . '>' . _OLEDRION_CMD_STATE3 . "</option>
        <option value='3'" . $selected[3] . '>' . _OLEDRION_CMD_STATE4 . "</option>
        <option value='4'" . $selected[4] . '>' . _OLEDRION_CMD_STATE5 . "</option>
        <option value='5'" . $selected[5] . '>' . _OLEDRION_CMD_STATE6 . "</option>
        <option value='6'" . $selected[6] . '>' . _OLEDRION_CMD_STATE7 . "</option>
        <option value='7'" . $selected[7] . '>' . _OLEDRION_CMD_STATE8 . "</option>
        <option value='8'" . $selected[8] . '>' . _OLEDRION_CMD_STATE9 . "</option>
        </select> <input type='hidden' name='op' id='op' value='orders'><input type='submit' name='btnfilter' id='btnfilter' value='" . _AM_OLEDRION_FILTER . "'></form>";
        $confValidateOrder = Oledrion\Utility::javascriptLinkConfirm(_AM_OLEDRION_CONF_VALIDATE);
        $confPackOrder     = Oledrion\Utility::javascriptLinkConfirm(_AM_OLEDRION_CONF_PACK);
        $confSubmitOrder   = Oledrion\Utility::javascriptLinkConfirm(_AM_OLEDRION_CONF_SUBMIT);
        $confDeliveryOrder = Oledrion\Utility::javascriptLinkConfirm(_AM_OLEDRION_CONF_DELIVERY);
        echo "<tr><td colspan='2' align='left'>";
        if (isset($pagenav) && is_object($pagenav)) {
            echo $pagenav->renderNav();
        } else {
            echo '&nbsp;';
        }
        $exportFormats = glob(OLEDRION_PATH . 'class/Exports/*.php');
        $formats       = [];
        foreach ($exportFormats as $format) {
            if (false === strpos($format, 'Export.php')) {
                $exportName = basename(str_replace('.php', '', $format));
                $formats[]  = '<option value="' . $exportName . '">' . $exportName . '</option>';
            }
        }
        echo "</td><td><form method='post' action='$baseurl' name='frmexport' id='frmexport'>"
             . _AM_OLEDRION_CSV_EXPORT
             . "<input type='hidden' name='op' id='op' value='orders'><input type='hidden' name='action' id='action' value='export'><input type='hidden' name='cmdtype' id='cmdtype' value='$filter3'><select name='exportfilter' id='exportfilter' size='1'>"
             . implode("\n", $formats)
             . "</select> <input type='submit' name='btngoexport' id='btngoexport' value='"
             . _AM_OLEDRION_OK
             . "'></form></td><td align='right' colspan='2'>"
             . $form
             . "</td></tr>\n";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DATE . "</th><th align='center'>" . _AM_OLEDRION_CLIENT . "</th><th align='center'>" . _AM_OLEDRION_TOTAL_SHIPP . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($orders as $item) {
            $id        = $item->getVar('cmd_id');
            $class     = ('even' === $class) ? 'odd' : 'even';
            $date      = formatTimestamp(strtotime($item->getVar('cmd_date')), 's');
            $actions   = [];
            $actions[] = "<a target='_blank' href='" . OLEDRION_URL . 'invoice.php?id=' . $id . "' title='" . _OLEDRION_DETAILS . "'>" . $icons['details'] . '</a>';
            $actions[] = "<a target='_blank' href='$baseurl?op=orders&action=print&id=" . $id . "' title='" . _OLEDRION_PRINT_VERSION . "'>" . $icons['print'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=validate&id=" . $id . "' " . $confValidateOrder . " title='" . _OLEDRION_VALIDATE_COMMAND . "'>" . $icons['ok'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=pack&id=" . $id . "' " . $confPackOrder . " title='" . _OLEDRION_PACK . "'>" . $icons['package'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=submit&id=" . $id . "' " . $confSubmitOrder . " title='" . _OLEDRION_SUBMIT . "'>" . $icons['submit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=delivery&id=" . $id . "' " . $confDeliveryOrder . " title='" . _OLEDRION_DELIVERY . "'>" . $icons['delivery'] . '</a>';
            $actions[] = "<a href='$baseurl?op=orders&action=track&id=" . $id . "' title='" . _OLEDRION_TRACK . "'>" . $icons['track'] . '</a>';
            $gift      = $item->getVar('cmd_gift') ?: '';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>"
                 . $id
                 . "</td><td align='center'>"
                 . $date
                 . "</td><td align='center'>"
                 . $item->getVar('cmd_lastname')
                 . ' '
                 . $item->getVar('cmd_firstname')
                 . ' '
                 . $gift
                 . "</td><td align='center'>"
                 . $oledrionCurrency->amountForDisplay($item->getVar('cmd_total', 'n'))
                 . ' / '
                 . $oledrionCurrency->amountForDisplay($item->getVar('cmd_shipping'))
                 . "</td><td align='center'>"
                 . implode(' ', $actions)
                 . "</td>\n";
            echo "<tr>\n";
            $totalOrder += (float)$item->getVar('cmd_total', 'n');
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='$class'><td colspan='2' align='center'><b>" . _OLEDRION_TOTAL . "</b></td><td>&nbsp;</td><td align='right'><b>" . $oledrionCurrency->amountForDisplay($totalOrder) . '</b></td><td>&nbsp;</td></tr>';
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        }
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (0 == $id) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $item = $commandsHandler->get($id);
        if (is_object($item)) {
            xoops_confirm(['op' => 'orders', 'action' => 'remove', 'id' => $id], 'index.php', _AM_OLEDRION_CONF_DELITEM);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'remove': // Suppression effective d'une commande
        // ****************************************************************************************************************
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item       = $commandsHandler->get($id);
        if (is_object($item)) {
            $res = $commandsHandler->removeOrder($item);
            if ($res) {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'validate': // Validation d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item       = $commandsHandler->get($id);
        if (is_object($item)) {
            $res = $commandsHandler->validateOrder($item);
            if ($res) {
                // Send sms
                if (Oledrion\Utility::getModuleOption('sms_validate')) {
                    $information['to']   = ltrim($item->getVar('cmd_mobile'), 0);
                    $information['text'] = Oledrion\Utility::getModuleOption('sms_validate_text');
                    $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
                }
                //
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'pack': // Validation d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item       = $commandsHandler->get($id);
        if (is_object($item)) {
            $res = $commandsHandler->packOrder($item);
            if ($res) {
                // Send sms
                if (Oledrion\Utility::getModuleOption('sms_validate')) {
                    $information['to']   = ltrim($item->getVar('cmd_mobile'), 0);
                    $information['text'] = Oledrion\Utility::getModuleOption('sms_pack_text');
                    $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
                }
                //
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'submit': // Validation d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item       = $commandsHandler->get($id);
        if (is_object($item)) {
            $res = $commandsHandler->submitOrder($item);
            if ($res) {
                // Send sms
                if (Oledrion\Utility::getModuleOption('sms_validate')) {
                    $information['to']   = ltrim($item->getVar('cmd_mobile'), 0);
                    $information['text'] = Oledrion\Utility::getModuleOption('sms_submit_text');
                    $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
                }
                //
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delivery': // Validation d'une commande
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'orders';
        $item       = $commandsHandler->get($id);
        if (is_object($item)) {
            $res = $commandsHandler->deliveryOrder($item);
            if ($res) {
                // Send sms
                if (Oledrion\Utility::getModuleOption('sms_validate')) {
                    $information['to']   = ltrim($item->getVar('cmd_mobile'), 0);
                    $information['text'] = Oledrion\Utility::getModuleOption('sms_delivery_text');
                    $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
                }
                //
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'export': // Export des commandes au format CSV
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=orders');
        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU5, 4);

        $orderType      = \Xmf\Request::getInt('cmdtype', 0, 'POST');
        $exportFilter   = $_POST['exportfilter'];
        $exportFilename = OLEDRION_PATH . 'class/Exports/' . $exportFilter . '.php';
        if (file_exists($exportFilename)) {
            //require_once OLEDRION_PATH . 'class/Exports/Export.php';
            //require_once $exportFilename;
            $className = '\\XoopsModules\Oledrion\Exports\\' . ucfirst($exportFilter) . 'Export';
            if (class_exists($className)) {
                $export = new $className();
                $export->setOrderType($orderType);
                $result = $export->export();
                if (true === $result) {
                    echo "<a href='" . $export->getDownloadUrl() . "'>" . _AM_OLEDRION_EXPORT_READY . '</a>';
                }
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_11);
        }
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'track': // track
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=orders');
        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU5, 4);
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $item = $commandsHandler->get($id);

        $sform = new \XoopsThemeForm(_OLEDRION_TRACK, 'frmproduct', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new \XoopsFormHidden('op', 'orders'));
        $sform->addElement(new \XoopsFormHidden('action', 'savetrack'));
        $sform->addElement(new \XoopsFormHidden('cmd_id', $item->getVar('cmd_id')));
        $sform->addElement(new \XoopsFormText(_OLEDRION_TRACK, 'cmd_track', 50, 255, $item->getVar('cmd_track', 'e')), true);
        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _SUBMIT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();

        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'savetrack': // save track
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('cmd_id', 0, 'POST');
        $item       = $commandsHandler->get($id);
        $opRedirect = 'orders';
        if (!is_object($item)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
        }
        $item->setVar('cmd_track', $_POST['cmd_track']);
        $res = $commandsHandler->insert($item);
        if ($res) {
            // Send sms
            if (Oledrion\Utility::getModuleOption('sms_track')) {
                $information['to']   = ltrim($item->getVar('cmd_mobile'), 0);
                $information['text'] = sprintf(Oledrion\Utility::getModuleOption('sms_track_text'), $_POST['cmd_track']);
                $sms                 = \XoopsModules\Oledrion\Sms::sendSms($information);
            }
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'print': // Print invoice
        // ****************************************************************************************************************
        xoops_cp_header();
        error_reporting(0);
        @$xoopsLogger->activated = false;
        $cmdId = \Xmf\Request::getInt('id', 0, 'GET');
        if (0 == $cmdId) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $order = null;
        $order = $commandsHandler->get($cmdId);
        $caddy = $tmp = $products = $vats = $manufacturers = $tmp2 = $manufacturers = $productsManufacturers = [];

        // Récupération des TVA
        $vats = $vatHandler->getAllVats(new Oledrion\Parameters());

        // Récupération des caddy associés
        $caddy = $caddyHandler->getCaddyFromCommand($cmdId);
        if (0 == count($caddy)) {
            Oledrion\Utility::redirect(_OLEDRION_ERROR11, 'index.php', 6);
        }

        // Récupération de la liste des produits associés
        foreach ($caddy as $item) {
            $tmp[] = $item->getVar('caddy_product_id');
        }

        // Recherche des produits ***********************************************************************************************
        $products = $productsHandler->getProductsFromIDs($tmp, true);

        // Recherche des fabricants **********************************************************************************************
        $tmp2 = $productsmanuHandler->getFromProductsIds($tmp);
        $tmp  = [];
        foreach ($tmp2 as $item) {
            $tmp[]                                                   = $item->getVar('pm_manu_id');
            $productsManufacturers[$item->getVar('pm_product_id')][] = $item;
        }
        $manufacturers = $manufacturerHandler->getManufacturersFromIds($tmp);
        //        $handlers          = HandlerManager::getInstance();
        $oledrionCurrency = Oledrion\Currency::getInstance();
        // Informations sur la commande ***************************************************************************************
        foreach ($caddy as $itemCaddy) {
            $productForTemplate = $tblJoin = $productManufacturers = $productAttributes = [];
            $product            = $products[$itemCaddy->getVar('caddy_product_id')];
            $productForTemplate = $product->toArray(); // Produit
            // Get cat title
            $cat                                     = $categoryHandler->get($productForTemplate['product_cid'])->toArray();
            $productForTemplate['product_cat_title'] = $cat['cat_title'];
            // Est-ce qu'il y a des attributs ?
            if ($caddyAttributesHandler->getAttributesCountForCaddy($itemCaddy->getVar('caddy_id')) > 0) {
                $productAttributes = $caddyAttributesHandler->getFormatedAttributesForCaddy($itemCaddy->getVar('caddy_id'), $product);
            }
            $productForTemplate['product_attributes']       = $productAttributes;
            $productForTemplate['product_attributes_count'] = count($productAttributes[0]['attribute_options']);

            $productManufacturers = $productsManufacturers[$product->getVar('product_id')];
            foreach ($productManufacturers as $oledrion_productsmanu) {
                if (isset($manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')])) {
                    $manufacturer = $manufacturers[$oledrion_productsmanu->getVar('pm_manu_id')];
                    $tblJoin[]    = $manufacturer->getVar('manu_commercialname') . ' ' . $manufacturer->getVar('manu_name');
                }
            }
            if (count($tblJoin) > 0) {
                $productForTemplate['product_joined_manufacturers'] = implode(', ', $tblJoin);
            }
            $productForTemplate['product_caddy'] = $itemCaddy->toArray();
            if ($productForTemplate['product_final_price_ttc']) {
                $discount = ($productForTemplate['product_caddy']['caddy_qte'] * $productForTemplate['product_final_price_ttc']) - ((int)$productForTemplate['product_caddy']['caddy_price']);
                $discount = $discount / $productForTemplate['product_caddy']['caddy_qte'];
            } else {
                $discount = 0;
            }
            $productForTemplate['product_caddy']['caddy_price_t'] = $oledrionCurrency->amountForDisplay($discount);
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
        $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/oledrion/templates/admin/oledrion_order_print.tpl');
        exit();
        xoops_cp_footer();
        break;
}
