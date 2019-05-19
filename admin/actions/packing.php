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
        $adminObject->displayNavigation('index.php?op=packing');

        $start   = \Xmf\Request::getInt('start', 0, 'GET');
        $packing = [];
        $form    = "<form method='post' action='$baseurl' name='frmaddpacking' id='frmaddpacking'><input type='hidden' name='op' id='op' value='packing'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                   . _AM_OLEDRION_ADD_ITEM
                   . "'></form>";
        echo $form;
        //        Oledrion\Utility::htitle(_MI_OLEDRION_ADMENU18, 4);
        $packing = $packingHandler->getAllPacking(new Oledrion\Parameters([
                                                                              'start' => $start,
                                                                              'limit' => $limit,
                                                                          ]));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_PACKING_TITLE . "</th><th align='center'>" . _OLEDRION_PRICE . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($packing as $item) {
            $id        = $item->getVar('packing_id');
            $class     = ('even' === $class) ? 'odd' : 'even';
            $actions   = [];
            $actions[] = "<a href='$baseurl?op=packing&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icons['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=packing&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icons['delete'] . '</a>';
            $online    = 1 == $item->getVar('packing_online') ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('packing_title') . "</td><td align='center'>" . $item->getVar('packing_price') . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
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
            $title = _AM_OLEDRION_PACKING_EDIT;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $packingHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_PACKING_ADD;
            $item         = $packingHandler->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new \XoopsThemeForm($title, 'frmaddpacking', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'packing'));
        $sform->addElement(new \XoopsFormHidden('action', 'save'));
        $sform->addElement(new \XoopsFormHidden('packing_id', $item->getVar('packing_id')));
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PACKING_TITLE, 'packing_title', 50, 150, $item->getVar('packing_title', 'e')), true);
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PACKING_WIDTH, 'packing_width', 20, 20, $item->getVar('packing_width', 'e')), false);
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PACKING_LENGTH, 'packing_length', 20, 20, $item->getVar('packing_length', 'e')), false);
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PACKING_WEIGHT, 'packing_weight', 20, 20, $item->getVar('packing_weight', 'e')), false);
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
        $editor = Oledrion\Utility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'packing_description', $item->getVar('packing_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_PACKING_PRICE, 'packing_price', 20, 20, $item->getVar('packing_price', 'e')), false);
        $sform->addElement(new \XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'packing_online', $item->getVar('packing_online')), true);
        $buttonTray = new \XoopsFormElementTray('', '');
        $submit_btn = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $buttonTray->addElement($submit_btn);
        $sform->addElement($buttonTray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    case 'save':

        xoops_cp_header();
        $id = \Xmf\Request::getInt('packing_id', 0, 'POST');
        if (!empty($id)) {
            $edit = true;
            $item = $packingHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $packingHandler->create(true);
        }
        $opRedirect = 'packing';
        $item->setVars($_POST);

        if (\Xmf\Request::hasVar('delpicture', 'POST') && 1 == \Xmf\Request::getInt('delpicture', 0, 'POST')) {
            $item->deletePicture();
        }
        $destname = '';
        $res1     = Oledrion\Utility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (Oledrion\Utility::getModuleOption('resize_others')) {
                // Eventuellement on redimensionne l'image
                Oledrion\Utility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, Oledrion\Utility::getModuleOption('images_width'), Oledrion\Utility::getModuleOption('images_height'), true);
            }
            $item->setVar('packing_image', basename($destname));
        } else {
            if (false !== $res1) {
                echo $res1;
            }
        }
        $res = $packingHandler->insert($item);
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
        $packing = null;
        $packing = $packingHandler->get($id);
        if (!is_object($packing)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $packing->getVar('packing_title'));
        xoops_confirm(['op' => 'packing', 'action' => 'confdelete', 'id' => $id], 'index.php', $msg);

        break;
    case 'confdelete':

        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'packing';

        $item = null;
        $item = $packingHandler->get($id);
        if (is_object($item)) {
            $res = $packingHandler->delete($item);
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
