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
        $adminObject->displayNavigation('index.php?op=location');

        $start    = \Xmf\Request::getInt('start', 0, 'GET');
        $location = [];
        $form     = "<form method='post' action='$baseurl' name='frmaddlocation' id='frmaddlocation'><input type='hidden' name='op' id='op' value='location'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                    . _AM_OLEDRION_ADD_ITEM
                    . "'></form>";
        echo $form;
        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU19, 4);
        $location = $locationHandler->getAllLocation(new Oledrion\Parameters([
                                                                                 'start' => $start,
                                                                                 'limit' => $limit
                                                                             ]));
        $class    = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_LOCATION_TITLE . "</th><th align='center'>" . _AM_OLEDRION_LOCATION_TYPE . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($location as $item) {
            $id            = $item->getVar('location_id');
            $class         = ('even' === $class) ? 'odd' : 'even';
            $actions       = [];
            $actions[]     = "<a href='$baseurl?op=location&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icons['edit'] . '</a>';
            $actions[]     = "<a href='$baseurl?op=location&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
            $online        = 1 == $item->getVar('location_online') ? _YES : _NO;
            $location_type = _AM_OLEDRION_LOCATION_LOCATION;
            if ('parent' === $item->getVar('location_type')) {
                $location_type = _AM_OLEDRION_LOCATION_PARENT;
            }
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('location_title') . "</td><td align='center'>" . $location_type . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
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
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if ('edit' === $action) {
            $title = _AM_OLEDRION_LOCATION_EDIT;
            if (empty($id)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $locationHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_LOCATION_ADD;
            $item         = $locationHandler->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        // Get delivery methods
        $deliveres = $deliveryHandler->getLocationDelivery(new Oledrion\Parameters([
                                                                                       'limit'    => $limit,
                                                                                       'location' => $id
                                                                                   ]));
        if (empty($deliveres)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_LOCATION_DELIVERYADD, $baseurl, 5);
        }

        $sform = new \XoopsThemeForm($title, 'frmaddlocation', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'location'));
        $sform->addElement(new \XoopsFormHidden('action', 'save'));
        $sform->addElement(new \XoopsFormHidden('location_id', $item->getVar('location_id')));
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_LOCATION_TITLE, 'location_title', 50, 150, $item->getVar('location_title', 'e')), true);
        $location_pid = $locationHandler->getAllPid(new Oledrion\Parameters());
        $mytree       = new Oledrion\XoopsObjectTree($location_pid, 'location_id', 'location_pid');

        if (Oledrion\Utility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
            $select_pid = $mytree->makeSelectElement('location_pid', 'location_title', '--', $item->getVar('location_pid'), true, 0, '', _AM_OLEDRION_LOCATION_PID);
            $sform->addElement($select_pid);
        } else {
            $select_pid = $mytree->makeSelBox('location_pid', 'location_title', '-', $item->getVar('location_pid'), true);
            $sform->addElement(new \XoopsFormLabel(_AM_OLEDRION_LOCATION_PID, $select_pid), false);
        }
        $product_type = new \XoopsFormSelect(_AM_OLEDRION_LOCATION_TYPE, 'location_type', $item->getVar('location_type'));
        $product_type->addOption('location', _AM_OLEDRION_LOCATION_LOCATION);
        $product_type->addOption('parent', _AM_OLEDRION_LOCATION_PARENT);
        $sform->addElement($product_type, true);
        $sform->addElement(new \XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'location_online', $item->getVar('location_online')), true);

        $delivery_options = new \XoopsFormElementTray(_AM_OLEDRION_LOCATION_DELIVERY, '<br>');
        foreach ($deliveres as $delivery) {
            if (isset($delivery['ld_id']) && is_array($delivery['ld_id'])) {
                $delivery_checkbox = new \XoopsFormCheckBox('', $delivery['delivery_id'] . '_ld_select', $delivery['ld_id']['delivery_select']);
                $delivery_checkbox->addOption(1, $delivery['delivery_title']);
                $delivery_options->addElement($delivery_checkbox);
                $delivery_options->addElement(new \XoopsFormText(_AM_OLEDRION_LOCATION_PRICE, $delivery['delivery_id'] . '_ld_price', 16, 16, $delivery['ld_id']['ld_price']));
                $delivery_options->addElement(new \XoopsFormText(_AM_OLEDRION_LOCATION_DELIVERY_TIME, $delivery['delivery_id'] . '_ld_delivery_time', 8, 8, $delivery['ld_id']['ld_delivery_time']));
                $delivery_options->addElement(new \XoopsFormHidden($delivery['delivery_id'] . '_ld_id', $delivery['ld_id']['ld_id']));
            } else {
                $delivery_checkbox = new \XoopsFormCheckBox('', $delivery['delivery_id'] . '_ld_select', '');
                $delivery_checkbox->addOption(1, $delivery['delivery_title']);
                $delivery_options->addElement($delivery_checkbox);
                $delivery_options->addElement(new \XoopsFormText(_AM_OLEDRION_LOCATION_PRICE, $delivery['delivery_id'] . '_ld_price', 16, 16, ''));
                $delivery_options->addElement(new \XoopsFormText(_AM_OLEDRION_LOCATION_DELIVERY_TIME, $delivery['delivery_id'] . '_ld_delivery_time', 8, 8, ''));
            }
        }

        $sform->addElement($delivery_options);
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
        $id = \Xmf\Request::getInt('location_id', 0, 'POST');
        if (!empty($id)) {
            $edit = true;
            $item = $locationHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $locationHandler->create(true);
        }

        $post              = $_POST;
        $location_delivery = [];
        $deliveres         = $deliveryHandler->getLocationDelivery(new Oledrion\Parameters([
                                                                                               'limit'    => $limit,
                                                                                               'location' => $id
                                                                                           ]));
        foreach ($deliveres as $delivery) {
            if (isset($post[$delivery['delivery_id'] . '_ld_select'])) {
                $location_delivery[$delivery['delivery_id']]['ld_location']      = $id;
                $location_delivery[$delivery['delivery_id']]['ld_delivery']      = (int)$delivery['delivery_id'];
                $location_delivery[$delivery['delivery_id']]['ld_price']         = (int)$post[$delivery['delivery_id'] . '_ld_price'];
                $location_delivery[$delivery['delivery_id']]['ld_delivery_time'] = (int)$post[$delivery['delivery_id'] . '_ld_delivery_time'];
            }
            unset($post[$delivery['delivery_id'] . '_ld_id'], $post[$delivery['delivery_id'] . '_ld_select'], $post[$delivery['delivery_id'] . '_ld_price'], $post[$delivery['delivery_id'] . '_ld_delivery_time']);
        }

        $opRedirect = 'location';
        $item->setVars($post);
        if ('parent' === $post['location_type']) {
            $item->setVar('location_pid', 0);
        }
        $res = $locationHandler->insert($item);

        $location_id = $item->getVar('location_id');
        // Save payments for each delivery type
        if ($edit) {
            // Suppression préalable
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('ld_location', $location_id, '='));
            $locationDeliveryHandler->deleteAll($criteria);
        }
        if (!empty($location_delivery)) {
            foreach ($location_delivery as $ld) {
                $item2 = $locationDeliveryHandler->create(true);
                $item2->setVar('ld_location', $location_id);
                $item2->setVar('ld_delivery', $ld['ld_delivery']);
                $item2->setVar('ld_price', $ld['ld_price']);
                $item2->setVar('ld_delivery_time', $ld['ld_delivery_time']);
                $res1 = $locationDeliveryHandler->insert($item2);
            }
        }

        if ($res) {
            Oledrion\Utility::updateCache();
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }

        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'delete':
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (0 == $id) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $location = null;
        $location = $locationHandler->get($id);
        if (!is_object($location)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $location->getVar('location_title'));
        xoops_confirm(['op' => 'location', 'action' => 'confdelete', 'id' => $id], 'index.php', $msg);
        break;

    case 'confdelete':

        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'location';

        $item = null;
        $item = $locationHandler->get($id);
        if (is_object($item)) {
            //Delete location_delivery info
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('ld_location', $item->getVar('location_id')));
            $locationDeliveryHandler->deleteAll($criteria);
            // Delete delivery
            $res = $locationHandler->delete($item);
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
