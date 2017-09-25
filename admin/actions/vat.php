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

/**
 * Actions relatives aux TVA (affichage, édition, suppression)
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des TVA
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=vat');

        $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $vats  = [];
        $form  = "<form method='post' action='$baseurl' name='frmaddvat' id='frmaddvat'><input type='hidden' name='op' id='op' value='vat'><input type='hidden' name='action' id='action' value='add'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='"
                 . _AM_OLEDRION_ADD_ITEM
                 . "'></form>";
        echo $form;
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU1, 4);
        $vats  = $h_oledrion_vat->getAllVats(new Oledrion_parameters(['start' => $start, 'limit' => $limit]));
        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_RATE . "</th><th align='center'>" . _AM_OLEDRION_COUNTRY . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($vats as $item) {
            $id        = $item->getVar('vat_id');
            $class     = ('even' === $class) ? 'odd' : 'even';
            $actions   = [];
            $actions[] = "<a href='$baseurl?op=vat&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=vat&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $id . "</td><td align='center'>" . $oledrion_Currency->amountInCurrency($item->getVar('vat_rate')) . "</td><td align='center'>" . ucfirst($item->getVar('vat_country')) . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'add': // Ajout d'une TVA
    case 'edit': // Edition d'une TVA
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(2);
        if ('edit' === $action) {
            $title = _AM_OLEDRION_EDIT_VAT;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_vat->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_ADD_VAT;
            $item         = $h_oledrion_vat->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $sform = new XoopsThemeForm($title, 'frmaddvat', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'vat'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('vat_id', $item->getVar('vat_id')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_RATE, 'vat_rate', 10, 15, $item->getVar('vat_rate', 'e')), true);
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_COUNTRY, 'vat_country', 35, 128, $item->getVar('vat_country', 'e')), true);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'une TVA
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['vat_id']) ? (int)$_POST['vat_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_vat->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_vat->create(true);
        }
        $opRedirect = 'vat';
        $item->setVars($_POST);
        $res = $h_oledrion_vat->insert($item);
        if ($res) {
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une TVA
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'vat';
        // On vérifie que cette TVA n'est pas utilisée par des produits
        $cnt = $h_oledrion_vat->getVatProductsCount($id);
        if (0 == $cnt) {
            $item = null;
            $item = $h_oledrion_vat->get($id);
            if (is_object($item)) {
                $res = $h_oledrion_vat->deleteVat($item);
                if ($res) {
                    OledrionUtility::updateCache();
                    OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                    OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_2, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
