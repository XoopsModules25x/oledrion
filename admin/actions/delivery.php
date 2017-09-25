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
        $adminObject->displayNavigation('index.php?op=delivery');

        $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $delivery = [];
        $form     = "<form method='post' action='$baseurl' name='frmadddelivery' id='frmadddelivery'><input type='hidden' name='op' id='op' value='delivery'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                    . _AM_OLEDRION_ADD_ITEM
                    . "'></form>";
        echo $form;
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU20, 4);
        $delivery = $h_oledrion_delivery->getAllDelivery(new Oledrion_parameters([
                                                                                     'start' => $start,
                                                                                     'limit' => $limit
                                                                                 ]));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DELIVERY_TITLE . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($delivery as $item) {
            $id        = $item->getVar('delivery_id');
            $class     = ('even' === $class) ? 'odd' : 'even';
            $actions   = [];
            $actions[] = "<a href='$baseurl?op=delivery&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=delivery&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            $online    = 1 == $item->getVar('delivery_online') ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('delivery_title') . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'add':
    case 'edit':
        xoops_cp_header();
        if ('edit' === $action) {
            $title = _AM_OLEDRION_DELIVERY_EDIT;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_delivery->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_DELIVERY_ADD;
            $item         = $h_oledrion_delivery->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new XoopsThemeForm($title, 'frmadddelivery', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'delivery'));
        $sform->addElement(new XoopsFormHidden('action', 'save'));
        $sform->addElement(new XoopsFormHidden('delivery_id', $item->getVar('delivery_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_DELIVERY_TITLE, 'delivery_title', 50, 150, $item->getVar('delivery_title', 'e')), true);

        // Add payment options ************************************************************
        $payments = $deliveryPayments = $payments_d = $deliveryPayments_d = [];

        $criteria = new Criteria('payment_id', 0, '<>');
        $criteria->setSort('payment_title');
        $payments = $h_oledrion_payment->getObjects($criteria);
        foreach ($payments as $oneitem) {
            $payments_d[$oneitem->getVar('payment_id')] = xoops_trim($oneitem->getVar('payment_title'));
        }

        if (empty($payments_d)) {
            OledrionUtility::redirect(_AM_OLEDRION_DELIVERY_PAYMENTADD, $baseurl, 5);
        }

        if ($edit) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('dp_delivery', $item->getVar('delivery_id'), '='));
            $deliveryPayments = $h_oledrion_delivery_payment->getObjects($criteria);
            foreach ($deliveryPayments as $oneproduct) {
                $deliveryPayments_d[] = $oneproduct->getVar('dp_payment');
            }
        }
        $paymentSelect = new XoopsFormSelect(_AM_OLEDRION_DELIVERY_PAYMENT, 'payments', $deliveryPayments_d, 5, true);
        $paymentSelect->addOptionArray($payments_d);
        $paymentSelect->setDescription(_AM_OLEDRION_SELECT_HLP);
        $sform->addElement($paymentSelect, true);

        if ('edit' === $action && $item->pictureExists()) {
            $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_CURRENT_PICTURE, '<br>');
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getPictureUrl() . "' alt='' border='0'>"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new XoopsFormFile(_AM_OLEDRION_PICTURE, 'attachedfile', OledrionUtility::getModuleOption('maxuploadsize')), false);
        $editor = OledrionUtility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'delivery_description', $item->getVar('delivery_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'delivery_online', $item->getVar('delivery_online')), true);
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
        $id = isset($_POST['delivery_id']) ? (int)$_POST['delivery_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_delivery->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_delivery->create(true);
        }
        $opRedirect = 'delivery';
        $item->setVars($_POST);
        if (isset($_POST['delpicture']) && 1 == (int)$_POST['delpicture']) {
            $item->deletePicture();
        }
        $destname = '';
        $res1     = OledrionUtility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (OledrionUtility::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                OledrionUtility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, OledrionUtility::getModuleOption('images_width'), OledrionUtility::getModuleOption('images_height'), true);
            }
            $item->setVar('delivery_image', basename($destname));
        } else {
            if (false !== $res1) {
                echo $res1;
            }
        }
        $res = $h_oledrion_delivery->insert($item);

        $delivery_id = $item->getVar('delivery_id');

        // Save payments for each delivery type
        if ($edit) {
            // Suppression préalable
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('dp_delivery', $delivery_id, '='));
            $h_oledrion_delivery_payment->deleteAll($criteria);
        }
        if (isset($_POST['payments'])) {
            foreach ($_POST['payments'] as $id2) {
                $item2 = $h_oledrion_delivery_payment->create(true);
                $item2->setVar('dp_delivery', $delivery_id);
                $item2->setVar('dp_payment', (int)$id2);
                $res1 = $h_oledrion_delivery_payment->insert($item2);
            }
        }

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
        if (0 == $id) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $delivery = null;
        $delivery = $h_oledrion_delivery->get($id);
        if (!is_object($delivery)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $delivery->getVar('delivery_title'));
        xoops_confirm(['op' => 'delivery', 'action' => 'confdelete', 'id' => $id], 'index.php', $msg);
        break;

    case 'confdelete':

        xoops_cp_header();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'delivery';

        $item = null;
        $item = $h_oledrion_delivery->get($id);
        if (is_object($item)) {
            //Delete delivery payment info
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('dp_delivery', $item->getVar('delivery_id')));
            $h_oledrion_delivery_payment->deleteAll($criteria);
            // Delete delivery
            $res = $h_oledrion_delivery->delete($item);
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
