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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

/**
 * Check is admin
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}

switch ($action) {
    case 'default':
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=payment');

        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $payment = array();
        $form    = "<form method='post' action='$baseurl' name='frmaddpayment' id='frmaddpayment'><input type='hidden' name='op' id='op' value='payment'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                   . _AM_OLEDRION_ADD_ITEM
                   . "'></form>";
        echo $form;
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU21, 4);
        $payment = $h_oledrion_payment->getAllPayment(new Oledrion_parameters(array(
                                                                                  'start' => $start,
                                                                                  'limit' => $limit
                                                                              )));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_TITLE . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_TYPE . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($payment as $item) {
            $id        = $item->getVar('payment_id');
            $class     = ($class === 'even') ? 'odd' : 'even';
            $actions   = array();
            $actions[] = "<a href='$baseurl?op=payment&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=payment&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            $online    = $item->getVar('payment_online') == 1 ? _YES : _NO;
            if ($item->getVar('payment_type') === 'online') {
                $payment_type = _AM_OLEDRION_PAYMENT_ONLINE . '( ' . $item->getVar('payment_gateway') . ' )';
            } else {
                $payment_type = _AM_OLEDRION_PAYMENT_OFFLINE;
            }
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('payment_title') . "</td><td align='center'>" . $payment_type . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='5' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'add':
    case 'edit':
        xoops_cp_header();
        if ($action === 'edit') {
            $title = _AM_OLEDRION_PAYMENT_EDIT;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_payment->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_PAYMENT_ADD;
            $item         = $h_oledrion_payment->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new XoopsThemeForm($title, 'frmaddpayment', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'payment'));
        $sform->addElement(new XoopsFormHidden('action', 'save'));
        $sform->addElement(new XoopsFormHidden('payment_id', $item->getVar('payment_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PAYMENT_TITLE, 'payment_title', 50, 150, $item->getVar('payment_title', 'e')), true);
        $product_type = new XoopsFormSelect(_AM_OLEDRION_PAYMENT_TYPE, 'payment_type', $item->getVar('payment_type'));
        $product_type->addOption('offline', _AM_OLEDRION_PAYMENT_OFFLINE);
        $product_type->addOption('online', _AM_OLEDRION_PAYMENT_ONLINE);
        $sform->addElement($product_type, true);
        $payment_gateway = new XoopsFormSelect(_AM_OLEDRION_PAYMENT_GATEWAY, 'payment_gateway', $item->getVar('payment_gateway'));
        $payment_gateway->addOption('offline', _AM_OLEDRION_PAYMENT_GATEWAY_OFFLINE);
        $payment_gateway_list = Oledrion_gateways::getInstalledGatewaysList();
        foreach ($payment_gateway_list as $payment_gateway_item) {
            $payment_gateway->addOption($payment_gateway_item);
        }
        $sform->addElement($payment_gateway, true);
        if ($action === 'edit' && $item->pictureExists()) {
            $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_CURRENT_PICTURE, '<br>');
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getPictureUrl() . "' alt='' border='0'>"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new XoopsFormFile(_AM_OLEDRION_PICTURE, 'attachedfile', OledrionUtility::getModuleOption('maxuploadsize')), false);
        $editor = OledrionUtility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'payment_description', $item->getVar('payment_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'payment_online', $item->getVar('payment_online')), true);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'save':
        xoops_cp_header();
        $id = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_payment->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_payment->create(true);
        }
        $opRedirect = 'payment';
        $item->setVars($_POST);

        if ($_POST['payment_type'] === 'offline') {
            $item->setVar('payment_gateway', 'offline');
        }

        if ($_POST['payment_type'] === 'online'
            && !in_array($_POST['payment_gateway'], Oledrion_gateways::getInstalledGatewaysList())) {
            $item->setVar('payment_gateway', Oledrion_gateways::getDefaultGateway());
        }

        if (isset($_POST['delpicture']) && (int)$_POST['delpicture'] == 1) {
            $item->deletePicture();
        }
        $destname = '';
        $res1     = OledrionUtility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (OledrionUtility::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                OledrionUtility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, OledrionUtility::getModuleOption('images_width'), OledrionUtility::getModuleOption('images_height'), true);
            }
            $item->setVar('payment_image', basename($destname));
        } else {
            if ($res1 !== false) {
                echo $res1;
            }
        }
        $res = $h_oledrion_payment->insert($item);
        if ($res) {
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    case 'delete':
        xoops_cp_header();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id == 0) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $payment = null;
        $payment = $h_oledrion_payment->get($id);
        if (!is_object($payment)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $payment->getVar('payment_title'));
        xoops_confirm(array('op' => 'payment', 'action' => 'confdelete', 'id' => $id), 'index.php', $msg);

        break;

    case 'confdelete':

        xoops_cp_header();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'payment';

        $item = null;
        $item = $h_oledrion_payment->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_payment->delete($item);
            if ($res) {
                OledrionUtility::updateCache();
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
