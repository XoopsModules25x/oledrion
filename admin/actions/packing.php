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
        $adminObject->displayNavigation('index.php?op=packing');

        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $packing = array();
        $form    = "<form method='post' action='$baseurl' name='frmaddpacking' id='frmaddpacking'><input type='hidden' name='op' id='op' value='packing'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                   . _AM_OLEDRION_ADD_ITEM
                   . "'></form>";
        echo $form;
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU18, 4);
        $packing = $h_oledrion_packing->getAllPacking(new Oledrion_parameters(array(
                                                                                  'start' => $start,
                                                                                  'limit' => $limit
                                                                              )));

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_PACKING_TITLE . "</th><th align='center'>" . _OLEDRION_PRICE . "</th><th align='center'>" . _OLEDRION_ONLINE . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($packing as $item) {
            $id        = $item->getVar('packing_id');
            $class     = ($class === 'even') ? 'odd' : 'even';
            $actions   = array();
            $actions[] = "<a href='$baseurl?op=packing&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=packing&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            $online    = $item->getVar('packing_online') == 1 ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('packing_title') . "</td><td align='center'>" . $item->getVar('packing_price') . "</td><td align='center'>" . $online . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
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
            $title = _AM_OLEDRION_PACKING_EDIT;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_packing->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_PACKING_ADD;
            $item         = $h_oledrion_packing->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new XoopsThemeForm($title, 'frmaddpacking', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'packing'));
        $sform->addElement(new XoopsFormHidden('action', 'save'));
        $sform->addElement(new XoopsFormHidden('packing_id', $item->getVar('packing_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PACKING_TITLE, 'packing_title', 50, 150, $item->getVar('packing_title', 'e')), true);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PACKING_WIDTH, 'packing_width', 20, 20, $item->getVar('packing_width', 'e')), false);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PACKING_LENGTH, 'packing_length', 20, 20, $item->getVar('packing_length', 'e')), false);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PACKING_WEIGHT, 'packing_weight', 20, 20, $item->getVar('packing_weight', 'e')), false);
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
        $editor = OledrionUtility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'packing_description', $item->getVar('packing_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_PACKING_PRICE, 'packing_price', 20, 20, $item->getVar('packing_price', 'e')), false);
        $sform->addElement(new XoopsFormRadioYN(_OLEDRION_ONLINE_HLP, 'packing_online', $item->getVar('packing_online')), true);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform =& OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    case 'save':
        xoops_cp_header();
        $id = isset($_POST['packing_id']) ? (int)$_POST['packing_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_packing->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_packing->create(true);
        }
        $opRedirect = 'packing';
        $item->setVars($_POST);

        if (isset($_POST['delpicture']) && (int)$_POST['delpicture'] == 1) {
            $item->deletePicture();
        }
        $destname = '';
        $res1     = OledrionUtility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1) {
            if (OledrionUtility::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                OledrionUtility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, OledrionUtility::getModuleOption('images_width'), OledrionUtility::getModuleOption('images_height'), true);
            }
            $item->setVar('packing_image', basename($destname));
        } else {
            if ($res1 !== false) {
                echo $res1;
            }
        }
        $res = $h_oledrion_packing->insert($item);
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
        $packing = null;
        $packing = $h_oledrion_packing->get($id);
        if (!is_object($packing)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_ITEM, $packing->getVar('packing_title'));
        xoops_confirm(array('op' => 'packing', 'action' => 'confdelete', 'id' => $id), 'index.php', $msg);

        break;

    case 'confdelete':

        xoops_cp_header();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'packing';

        $item = null;
        $item = $h_oledrion_packing->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_packing->delete($item);
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
