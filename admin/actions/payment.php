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

use XoopsModules\Oledrion;

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

        $start   = \Xmf\Request::getInt('start', 0, 'GET');
        $payment = [];
        $form    = "<form method='post' action='$baseurl' name='frmaddpayment' id='frmaddpayment'><input type='hidden' name='op' id='op' value='payment'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                   . _AM_OLEDRION_ADD_ITEM
                   . "'></form>";
        echo $form;
        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU21, 4);
        $payment = $paymentHandler->getAllPayment(new Oledrion\Parameters([
                                                                                  'start' => $start,
                                                                                  'limit' => $limit
                                                                              ]));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_TITLE . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_TYPE . "</th><th align='center'>" . _AM_OLEDRION_PAYMENT_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($payment as $item) {
            $id        = $item->getVar('payment_id');
            $class     = ('even' === $class) ? 'odd' : 'even';
            $actions   = [];
            $actions[] = "<a href='$baseurl?op=payment&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icons['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=payment&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
            $online    = 1 == $item->getVar('payment_online') ? _YES : _NO;
            $payment_type = _AM_OLEDRION_PAYMENT_OFFLINE;
            if ('online' === $item->getVar('payment_type')) {
                $payment_type = _AM_OLEDRION_PAYMENT_ONLINE . '( ' . $item->getVar('payment_gateway') . ' )';
            }
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('payment_title') . "</td><td align='center'>" . $payment_type . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='5' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'add':
    case 'edit':
        xoops_cp_header();
        if ('edit' === $action) {
            $title = _AM_OLEDRION_PAYMENT_EDIT;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $paymentHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_PAYMENT_ADD;
            $item         = $paymentHandler->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new \XoopsThemeForm($title, 'frmaddpayment', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'payment'));
        $sform->addElement(new \XoopsFormHidden('action', 'save'));
        $sform->addElement(new \XoopsFormHidden('payment_id', $item->getVar('payment_id')));
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PAYMENT_TITLE, 'payment_title', 50, 150, $item->getVar('payment_title', 'e')), true);
        $product_type = new \XoopsFormSelect(_AM_OLEDRION_PAYMENT_TYPE, 'payment_type', $item->getVar('payment_type'));
        $product_type->addOption('offline', _AM_OLEDRION_PAYMENT_OFFLINE);
        $product_type->addOption('online', _AM_OLEDRION_PAYMENT_ONLINE);
        $sform->addElement($product_type, true);
        $payment_gateway = new \XoopsFormSelect(_AM_OLEDRION_PAYMENT_GATEWAY, 'payment_gateway', $item->getVar('payment_gateway'));
        $payment_gateway->addOption('offline', _AM_OLEDRION_PAYMENT_GATEWAY_OFFLINE);
        $payment_gateway_list = Oledrion\Gateways::getInstalledGatewaysList();
        foreach ($payment_gateway_list as $payment_gateway_item) {
            $payment_gateway->addOption($payment_gateway_item);
        }
        $sform->addElement($payment_gateway, true);
        if ('edit' === $action && $item->pictureExists()) {
            $pictureTray = new \XoopsFormElementTray(_AM_OLEDRION_CURRENT_PICTURE, '<br>');
            $pictureTray->addElement(new \XoopsFormLabel('', "<img src='" . $item->getPictureUrl() . "' alt='' border='0'>"));
            $deleteCheckbox = new \XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new \XoopsFormFile(_AM_OLEDRION_PICTURE, 'attachedfile', Oledrion\Utility::getModuleOption('maxuploadsize')), false);
        $editor = Oledrion\Utility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'payment_description', $item->getVar('payment_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new \XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'payment_online', $item->getVar('payment_online')), true);
        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'save':
        xoops_cp_header();
        $id = \Xmf\Request::getInt('payment_id', 0, 'POST');
        if (!empty($id)) {
            $edit = true;
            $item = $paymentHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $paymentHandler->create(true);
        }
        $opRedirect = 'payment';
        $item->setVars($_POST);

        if ('offline' === $_POST['payment_type']) {
            $item->setVar('payment_gateway', 'offline');
        }

        if ('online' === $_POST['payment_type']
            && !in_array($_POST['payment_gateway'], Oledrion\Gateways::getInstalledGatewaysList())) {
            $item->setVar('payment_gateway', Oledrion\Gateways::getDefaultGateway());
        }

        if (isset($_POST['delpicture']) && 1 == \Xmf\Request::getInt('delpicture', 0, 'POST')) {
            $item->deletePicture();
        }
        $destname = '';
        $res1     = Oledrion\Utility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (Oledrion\Utility::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                Oledrion\Utility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, Oledrion\Utility::getModuleOption('images_width'), Oledrion\Utility::getModuleOption('images_height'), true);
            }
            $item->setVar('payment_image', basename($destname));
        } else {
            if (false !== $res1) {
                echo $res1;
            }
        }
        $res = $paymentHandler->insert($item);
        if ($res) {
            Oledrion\Utility::updateCache();
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    case 'delete':
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (0 == $id) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $payment = null;
        $payment = $paymentHandler->get($id);
        if (!is_object($payment)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $payment->getVar('payment_title'));
        xoops_confirm(['op' => 'payment', 'action' => 'confdelete', 'id' => $id], 'index.php', $msg);

        break;

    case 'confdelete':

        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'payment';

        $item = null;
        $item = $paymentHandler->get($id);
        if (is_object($item)) {
            $res = $paymentHandler->delete($item);
            if ($res) {
                Oledrion\Utility::updateCache();
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
