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
 * Gestion des fichiers attachés aux produits (dans l'administration)
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
$opRedirect = 'files';

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Gestion des fichiers attachés
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=files');

        $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $form  = "<form method='post' action='$baseurl' name='frmadd' id='frmadd'><input type='hidden' name='op' id='op' value='files'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "'></form>";
        echo $form;
        //        OledrionUtility::htitle(_MI_OLEDRION_ADMENU11, 4);
        $itemsCount = $h_oledrion_files->getCount(); // Recherche du nombre total d'éléments
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=files');
        }
        $items = $products = $productsIds = [];
        $items = $h_oledrion_files->getItems($start, $limit);
        foreach ($items as $item) {
            $productsIds[] = $item->getVar('file_product_id');
        }
        if (count($productsIds) > 0) {
            sort($productsIds);
            $productsIds = array_unique($productsIds);
            $products    = $h_oledrion_products->getProductsFromIDs($productsIds);
        }

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_OLEDRION_ID . "</th><th align='center'>" . _AM_OLEDRION_DESCRIPTION . "</th><th align='center'>" . _AM_OLEDRION_DISCOUNT_PRODUCT . "</th><th align='center'>" . _AM_OLEDRION_ACTION . '</th></tr>';
        foreach ($items as $item) {
            $class     = ('even' === $class) ? 'odd' : 'even';
            $id        = $item->getVar('file_id');
            $actions   = [];
            $actions[] = "<a href='$baseurl?op=files&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=files&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            $product = isset($products[$item->getVar('file_product_id')]) ? $products[$item->getVar('file_product_id')]->getVar('product_title') : '';
            echo "<td align='center'>" . $id . '</td>';
            echo '<td>' . $item->getVar('file_description') . "</td><td align='center'>" . $product . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        }
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'add': // Ajout d'un fichier
    case 'edit': // Edition d'un fichier
        // ****************************************************************************************************************
        xoops_cp_header();
        if ('edit' === $action) {
            $title = _AM_OLEDRION_EDIT_FILE;
            $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_files->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_ADD_FILE;
            $item         = $h_oledrion_files->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $products = [];
        $products = $h_oledrion_products->getList();
        $sform    = new XoopsThemeForm($title, 'frmaddfile', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new XoopsFormHidden('op', 'files'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('file_id', $item->getVar('file_id')));
        /*
                $file_product_id_select = new XoopsFormSelect(_OLEDRION_PRODUCT, 'file_product_id', $item->getVar('file_product_id', 'e'), 1, false);
                $file_product_id_select->addOptionArray($products);
                $sform->addElement($file_product_id_select, true);
        */
        $productsSelect = $h_oledrion_products->productSelector(new Oledrion_parameters([
                                                                                            'caption'  => _OLEDRION_PRODUCT,
                                                                                            'name'     => 'file_product_id',
                                                                                            'value'    => $item->getVar('file_product_id', 'e'),
                                                                                            'size'     => 1,
                                                                                            'multiple' => false,
                                                                                            'values'   => null,
                                                                                            'showAll'  => true,
                                                                                            'sort'     => 'product_title',
                                                                                            'order'    => 'ASC',
                                                                                            'formName' => 'frmaddfile'
                                                                                        ]));
        $sform->addElement($productsSelect, true);

        $sform->addElement(new XoopsFormText(_AM_OLEDRION_DESCRIPTION, 'file_description', 50, 255, $item->getVar('file_description', 'e')), true);

        if ('edit' === $action && '' !== trim($item->getVar('file_filename')) && $item->fileExists()) {
            $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_CURRENT_FILE, '<br>');
            $pictureTray->addElement(new XoopsFormLabel('', "<a href='" . $item->getURL() . "' target='_blank'>" . $item->getVar('file_filename') . '</a>'));
            $sform->addElement($pictureTray);
            unset($pictureTray);
        }
        $sform->addElement(new XoopsFormFile(_AM_OLEDRION_FILENAME, 'attachedfile', OledrionUtility::getModuleOption('maxuploadsize')), false);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'un fichier attaché
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['file_id']) ? (int)$_POST['file_id'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_files->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_files->create(true);
        }
        $item->setVars($_POST);
        $destname = '';
        $result   = OledrionUtility::uploadFile(0, OLEDRION_ATTACHED_FILES_PATH);
        if (true === $result) {
            $item->setVar('file_filename', basename($destname));
            $item->setVar('file_mimetype', OledrionUtility::getMimeType(OLEDRION_ATTACHED_FILES_PATH . '/' . $destname));
        } else {
            if (false !== $result) {
                OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB . '<br>' . $result, $baseurl . '?op=' . $opRedirect, 5);
            }
        }
        $res = $h_oledrion_files->insert($item);
        if ($res) {
            OledrionUtility::updateCache();
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'un fichier
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $item = null;
        $item = $h_oledrion_files->get($id);
        if (is_object($item)) {
            $res = $h_oledrion_files->deleteAttachedFile($item);
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
