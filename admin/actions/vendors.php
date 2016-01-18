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
 * @version     $Id: vendors.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Actions relatives aux vendeurs (affichage, édition, suppression)
 */
if (!defined("OLEDRION_ADMIN")) exit();
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des vendeurs
        // ****************************************************************************************************************
        xoops_cp_header();
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $vendors = array();
        $form = "<form method='post' action='$baseurl' name='frmaddvendor' id='frmaddvendor'><input type='hidden' name='op' id='op' value='vendors' /><input type='hidden' name='action' id='action' value='add' /><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "' /></form>";
        echo $form;
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU0, 4);
        $vendors = $h_oledrion_vendors->getAllVendors(new oledrion_parameters(array('start' => $start, 'limit' => $limit)));
        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _OLEDRION_VENDOR . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($vendors as $item) {
            $id = $item->getVar('vendor_id');
            $class = ($class == 'even') ? 'odd' : 'even';
            $actions = array();
            $actions[] = "<a href='$baseurl?op=vendors&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=vendors&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $item->getVar('vendor_name') . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='3' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'add': // Ajout d'un vendeur
    case 'edit': // Edition d'un vendeur
        // ****************************************************************************************************************
        xoops_cp_header();
        if ($action == 'edit') {
            $title = _AM_OLEDRION_EDIT_VENDOR;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (empty($id)) {
                oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_vendors->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_VENDOR;
            $item = $h_oledrion_vendors->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit = false;
        }
        $sform = new XoopsThemeForm($title, 'frmaddvendor', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'vendors'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('vendor_id', $item->getVar('vendor_id')));
        $sform->addElement(new XoopsFormText(_OLEDRION_VENDOR, 'vendor_name', 50, 150, $item->getVar('vendor_name', 'e')), true);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = oledrion_utils::formMarkRequiredFields($sform);
        $sform->display();
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'un vendeur (édition et ajout)
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_vendors->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_vendors->create(true);
        }
        $opRedirect = 'vendors';
        $item->setVars($_POST);
        $res = $h_oledrion_vendors->insert($item);
        if ($res) {
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'un vendeur
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'vendors';
        // On vérifie que ce vendeur n'est pas rattaché à des produits
        $cnt = $h_oledrion_vendors->getVendorProductsCount($id);
        if ($cnt == 0) {
            $item = null;
            $item = $h_oledrion_vendors->get($id);
            if (is_object($item)) {
                $res = $h_oledrion_vendors->deleteVendor($item);
                if ($res) {
                    oledrion_utils::updateCache();
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                    oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_6, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
