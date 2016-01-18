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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 * @version     $Id: delivery.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Check is admin
 */
if (!defined("OLEDRION_ADMIN")) exit();

switch ($action) {
    case 'default':
        xoops_cp_header();
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $delivery = array();
        $form = "<form method='post' action='$baseurl' name='frmadddelivery' id='frmadddelivery'><input type='hidden' name='op' id='op' value='delivery' /><input type='hidden' name='action' id='action' value='add' /><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "' /></form>";
        echo $form;
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU20, 4);
        $delivery = $h_oledrion_delivery->getAllDelivery(new oledrion_parameters(array('start' => $start, 'limit' => $limit)));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DELIVERY_TITLE . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($delivery as $item) {
            $id = $item->getVar('delivery_id');
            $class = ($class == 'even') ? 'odd' : 'even';
            $actions = array();
            $actions[] = "<a href='$baseurl?op=delivery&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=delivery&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            $online = $item->getVar('delivery_online') == 1 ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('delivery_title') . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'add':
    case 'edit':
        xoops_cp_header();
        if ($action == 'edit') {
            $title = _AM_OLEDRION_DELIVERY_EDIT;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (empty($id)) {
                oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_delivery->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_DELIVERY_ADD;
            $item = $h_oledrion_delivery->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit = false;
        }
        $sform = new XoopsThemeForm($title, 'frmadddelivery', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'delivery'));
        $sform->addElement(new XoopsFormHidden('action', 'save'));
        $sform->addElement(new XoopsFormHidden('delivery_id', $item->getVar('delivery_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_DELIVERY_TITLE, 'delivery_title', 50, 150, $item->getVar('delivery_title', 'e')), true);

        // Add payment options ************************************************************
        $payments = $deliveryPayments = $payments_d = $deliveryPayments_d = array();

        $criteria = new Criteria('payment_id', 0, '<>');
        $criteria->setSort('payment_title');
        $payments = $h_oledrion_payment->getObjects($criteria);
        foreach ($payments as $oneitem) {
            $payments_d[$oneitem->getVar('payment_id')] = xoops_trim($oneitem->getVar('payment_title'));
        }

        if (empty($payments_d)) {
            oledrion_utils::redirect(_AM_OLEDRION_DELIVERY_PAYMENTADD, $baseurl, 5);
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

        if ($action == 'edit' && $item->pictureExists()) {
            $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_CURRENT_PICTURE, '<br />');
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getPictureUrl() . "' alt='' border='0' />"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new XoopsFormFile(_AM_OLEDRION_PICTURE, 'attachedfile', oledrion_utils::getModuleOption('maxuploadsize')), false);
        $editor = oledrion_utils::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'delivery_description', $item->getVar('delivery_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'delivery_online', $item->getVar('delivery_online')), true);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = oledrion_utils::formMarkRequiredFields($sform);
        $sform->display();
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'save':
        xoops_cp_header();
        $id = isset($_POST['delivery_id']) ? intval($_POST['delivery_id']) : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_delivery->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_delivery->create(true);
        }
        $opRedirect = 'delivery';
        $item->setVars($_POST);
        if (isset($_POST['delpicture']) && intval($_POST['delpicture']) == 1) {
            $item->deletePicture();
        }
        $destname = '';
        $res1 = oledrion_utils::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (oledrion_utils::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                oledrion_utils::resizePicture(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, oledrion_utils::getModuleOption('images_width'), oledrion_utils::getModuleOption('images_height'), true);
            }
            $item->setVar('delivery_image', basename($destname));
        } else {
            if ($res1 !== false) {
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
                $item2->setVar('dp_payment', intval($id2));
                $res1 = $h_oledrion_delivery_payment->insert($item2);
            }
        }

        if ($res) {
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    case 'delete':
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id == 0) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $delivery = null;
        $delivery = $h_oledrion_delivery->get($id);
        if (!is_object($delivery)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $delivery->getVar('delivery_title'));
        xoops_confirm(array('op' => 'delivery', 'action' => 'confdelete', 'id' => $id), 'index.php', $msg);
        break;

    case 'confdelete':

        xoops_cp_header();
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
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
