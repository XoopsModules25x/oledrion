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
 * Gestion des catégories de produits
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}

switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Liste des catégories
        // ****************************************************************************************************************
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=categories');

        // Display categories **********************************************************************
        $categories = array();
        //        OledrionUtility::htitle(_AM_OLEDRION_CATEGORIES, 4);

        $categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
        $mytree     = new XoopsObjectTree($categories, 'cat_cid', 'cat_pid');

        if (OledrionUtility::checkVerXoops($module, '2.5.9')) {
            $categoriesSelect0 = $mytree->makeSelectElement('id', 'cat_title', '--', '', true, 0, '', '');
            $categoriesSelect  = $categoriesSelect0->render();
        } else {
            $categoriesSelect = $mytree->makeSelBox('id', 'cat_title', '-- ', '', true);
        }

        echo "<div class='even'><form method='post' name='quickaccess' id='quickaccess' action='$baseurl' >"
             . _AM_OLEDRION_LIST
             . " $categoriesSelect<input type='hidden' name='op' id='op' value='categories'><input type='radio' name='action' id='action' value='edit'>"
             . _EDIT
             . " <input type='radio' name='action' id='action' value='delete'>"
             . _DELETE
             . " <input type='submit' name='btnquick' id='btnquick' value='"
             . _GO
             . "'></form></div>\n";
        echo "<div class='odd' align='center'><form method='post' name='frmadd' id='frmadd' action='$baseurl' ><input type='hidden' name='op' id='op' value='categories'><input type='hidden' name='action' id='action' value='add'><input type='submit' name='btnadd' id='btnadd' value='"
             . _AM_OLEDRION_ADD_CATEG
             . "'></form></div>\n";
        echo "<br><br>\n";

        // Categories preferences *****************************************************************
        $chunk1    = OledrionUtility::getModuleOption('chunk1');
        $chunk2    = OledrionUtility::getModuleOption('chunk2');
        $chunk3    = OledrionUtility::getModuleOption('chunk3');
        $chunk4    = OledrionUtility::getModuleOption('chunk4');
        $positions = array(0 => _AM_OLEDRION_INVISIBLE, 1 => '1', 2 => '2', 3 => '3', 4 => '4');

        $sform = new XoopsThemeForm(_AM_OLEDRION_CATEG_CONFIG, 'frmchunk', $baseurl);
        $sform->addElement(new XoopsFormHidden('op', 'categories'));
        $sform->addElement(new XoopsFormHidden('action', 'savechunks'));
        $sform->addElement(new XoopsFormLabel(_AM_OLEDRION_CHUNK, _AM_OLEDRION_POSITION));

        $chunk = null;
        $chunk = new XoopsFormSelect(_MI_OLEDRION_CHUNK1, 'chunk1', $chunk1, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new XoopsFormSelect(_MI_OLEDRION_CHUNK2, 'chunk2', $chunk2, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new XoopsFormSelect(_MI_OLEDRION_CHUNK3, 'chunk3', $chunk3, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new XoopsFormSelect(_MI_OLEDRION_CHUNK4, 'chunk4', $chunk4, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _AM_OLEDRION_OK, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'savechunks': // Save chunks order
        // ****************************************************************************************************************
        oledrion_set_module_option('chunk1', (int)$_POST['chunk1']);
        oledrion_set_module_option('chunk2', (int)$_POST['chunk2']);
        oledrion_set_module_option('chunk3', (int)$_POST['chunk3']);
        oledrion_set_module_option('chunk4', (int)$_POST['chunk4']);
        OledrionUtility::updateCache();
        OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=categories');
        break;

    // ****************************************************************************************************************
    case 'add': // Ajout d'une catégorie
    case 'edit': // Edition d'une catégorie
        // ****************************************************************************************************************
        xoops_cp_header();

        if ($action === 'edit') {
            $title = _AM_OLEDRION_EDIT_CATEG;
            $id    = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if (empty($id)) {
                OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_cat->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_ADD_CATEG;
            $item         = $h_oledrion_cat->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $tbl_categories = $h_oledrion_cat->getAllCategories(new Oledrion_parameters());
        $mytree         = new XoopsObjectTree($tbl_categories, 'cat_cid', 'cat_pid');

        $sform = new XoopsThemeForm($title, 'frmcategory', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new XoopsFormHidden('op', 'categories'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('cat_cid', $item->getVar('cat_cid')));
        $sform->addElement(new XoopsFormText(_AM_OLEDRION_CATEG_TITLE, 'cat_title', 50, 255, $item->getVar('cat_title', 'e')), true);

        if (OledrionUtility::checkVerXoops($module, '2.5.9')) {
            $select_categ = $mytree->makeSelectElement('cat_pid', 'cat_title', '--', $item->getVar('cat_pid'), true, 0, '', _AM_OLEDRION_PARENT_CATEG);
            $sform->addElement($select_categ);
        } else {
            $select_categ = $mytree->makeSelBox('cat_pid', 'cat_title', '-', $item->getVar('cat_pid'), true);
            $sform->addElement(new XoopsFormLabel(_AM_OLEDRION_PARENT_CATEG, $select_categ), false);
        }

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
        $editor = OledrionUtility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'cat_description', $item->getVar('cat_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }

        $editor3 = OledrionUtility::getWysiwygForm(_AM_OLEDRION_FOOTER, 'cat_footer', $item->getVar('cat_footer', 'e'), 15, 60, 'footer_hidden');
        if ($editor3) {
            $sform->addElement($editor3, false);
        }

        $editor2 = OledrionUtility::getWysiwygForm(_MI_OLEDRION_ADVERTISEMENT, 'cat_advertisement', $item->getVar('cat_advertisement', 'e'), 15, 60, 'pub_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        // META Data
        if ($manual_meta) {
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_KEYWORDS, 'cat_metakeywords', 50, 255, $item->getVar('cat_metakeywords', 'e')), false);
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_DESCRIPTION, 'cat_metadescription', 50, 255, $item->getVar('cat_metadescription', 'e')), false);
            $sform->addElement(new XoopsFormText(_AM_OLEDRION_META_PAGETITLE, 'cat_metatitle', 50, 255, $item->getVar('cat_metatitle', 'e')), false);
        }

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform = OledrionUtility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'une catégorie
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['cat_cid']) ? (int)$_POST['cat_cid'] : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_cat->get($id);
            if (!is_object($item)) {
                OledrionUtility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $add = false;
        } else {
            $item = $h_oledrion_cat->create(true);
            $add  = true;
        }
        $opRedirect = 'categories';
        $item->setVars($_POST);

        if (isset($_POST['delpicture']) && (int)$_POST['delpicture'] == 1) {
            $item->deletePicture();
        }

        $destname = '';
        $res1     = OledrionUtility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if ($res1 === true) {
            if (OledrionUtility::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                OledrionUtility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, OledrionUtility::getModuleOption('images_width'), OledrionUtility::getModuleOption('images_height'), true);
            }
            $item->setVar('cat_imgurl', basename($destname));
        } else {
            if ($res1 !== false) {
                echo $res1;
            }
        }

        $res = $h_oledrion_cat->insert($item);
        if ($res) {
            OledrionUtility::updateCache();
            if ($add) {
                //$plugins = Oledrion_plugins::getInstance();
                //$plugins->fireAction(Oledrion_plugins::EVENT_ON_CATEGORY_CREATE, new Oledrion_parameters(array('category' => $item)));
            }
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            OledrionUtility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'delete': // Suppression d'une catégorie
        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(3);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id == 0) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $category = null;
        $category = $h_oledrion_cat->get($id);
        if (!is_object($category)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_CATEG, $category->getVar('cat_title'));
        xoops_confirm(array('op' => 'categories', 'action' => 'confdelete', 'id' => $id), 'index.php', $msg);
        break;

    // ****************************************************************************************************************
    case 'confdelete': //Suppression effective d'une catégorie
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (empty($id)) {
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'categories';
        // On vérifie que cette catégorie (et ses sous-catégories) ne sont pas utilisées par des produits
        $cnt = $h_oledrion_cat->getCategoryProductsCount($id);
        if ($cnt == 0) {
            $item = null;
            $item = $h_oledrion_cat->get($id);
            if (is_object($item)) {
                $res = $h_oledrion_cat->deleteCategory($item);
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
            OledrionUtility::redirect(_AM_OLEDRION_ERROR_4, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
