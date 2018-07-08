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

use XoopsModules\Oledrion;

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
        $categories = [];
        //        Oledrion\Utility::htitle(_AM_OLEDRION_CATEGORIES, 4);

        $categories = $categoryHandler->getAllCategories(new Oledrion\Parameters());
        $mytree     = new Oledrion\XoopsObjectTree($categories, 'cat_cid', 'cat_pid');

        if (Oledrion\Utility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
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
        $chunk1    = Oledrion\Utility::getModuleOption('chunk1');
        $chunk2    = Oledrion\Utility::getModuleOption('chunk2');
        $chunk3    = Oledrion\Utility::getModuleOption('chunk3');
        $chunk4    = Oledrion\Utility::getModuleOption('chunk4');
        $positions = [0 => _AM_OLEDRION_INVISIBLE, 1 => '1', 2 => '2', 3 => '3', 4 => '4'];

        $sform = new \XoopsThemeForm(_AM_OLEDRION_CATEG_CONFIG, 'frmchunk', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'categories'));
        $sform->addElement(new \XoopsFormHidden('action', 'savechunks'));
        $sform->addElement(new \XoopsFormLabel(_AM_OLEDRION_CHUNK, _AM_OLEDRION_POSITION));

        $chunk = null;
        $chunk = new \XoopsFormSelect(_MI_OLEDRION_CHUNK1, 'chunk1', $chunk1, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new \XoopsFormSelect(_MI_OLEDRION_CHUNK2, 'chunk2', $chunk2, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new \XoopsFormSelect(_MI_OLEDRION_CHUNK3, 'chunk3', $chunk3, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        unset($chunk);
        $chunk = new \XoopsFormSelect(_MI_OLEDRION_CHUNK4, 'chunk4', $chunk4, 1, false);
        $chunk->addOptionArray($positions);
        $sform->addElement($chunk, true);

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _AM_OLEDRION_OK, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    // ****************************************************************************************************************
    case 'savechunks': // Save chunks order

        // ****************************************************************************************************************
        oledrion_set_module_option('chunk1', \Xmf\Request::getInt('chunk1', 0, 'POST'));
        oledrion_set_module_option('chunk2', \Xmf\Request::getInt('chunk2', 0, 'POST'));
        oledrion_set_module_option('chunk3', \Xmf\Request::getInt('chunk3', 0, 'POST'));
        oledrion_set_module_option('chunk4', \Xmf\Request::getInt('chunk4', 0, 'POST'));
        Oledrion\Utility::updateCache();
        Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=categories');

        break;
    // ****************************************************************************************************************
    case 'add': // Ajout d'une catégorie

    case 'edit': // Edition d'une catégorie

        // ****************************************************************************************************************
        xoops_cp_header();

        if ('edit' === $action) {
            $title = _AM_OLEDRION_EDIT_CATEG;
            $id    = \Xmf\Request::getInt('id', 0, 'POST');
            if (empty($id)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $categoryHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title        = _AM_OLEDRION_ADD_CATEG;
            $item         = $categoryHandler->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit         = false;
        }
        $tbl_categories = $categoryHandler->getAllCategories(new Oledrion\Parameters());
        $mytree         = new Oledrion\XoopsObjectTree($tbl_categories, 'cat_cid', 'cat_pid');

        $sform = new \XoopsThemeForm($title, 'frmcategory', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new \XoopsFormHidden('op', 'categories'));
        $sform->addElement(new \XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new \XoopsFormHidden('cat_cid', $item->getVar('cat_cid')));
        $sform->addElement(new \XoopsFormText(_AM_OLEDRION_CATEG_TITLE, 'cat_title', 50, 255, $item->getVar('cat_title', 'e')), true);

        if (Oledrion\Utility::checkVerXoops($GLOBALS['xoopsModule'], '2.5.9')) {
            $select_categ = $mytree->makeSelectElement('cat_pid', 'cat_title', '--', $item->getVar('cat_pid'), true, 0, '', _AM_OLEDRION_PARENT_CATEG);
            $sform->addElement($select_categ);
        } else {
            $select_categ = $mytree->makeSelBox('cat_pid', 'cat_title', '-', $item->getVar('cat_pid'), true);
            $sform->addElement(new \XoopsFormLabel(_AM_OLEDRION_PARENT_CATEG, $select_categ), false);
        }

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
        $editor = Oledrion\Utility::getWysiwygForm(_AM_OLEDRION_DESCRIPTION, 'cat_description', $item->getVar('cat_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }

        $editor3 = Oledrion\Utility::getWysiwygForm(_AM_OLEDRION_FOOTER, 'cat_footer', $item->getVar('cat_footer', 'e'), 15, 60, 'footer_hidden');
        if ($editor3) {
            $sform->addElement($editor3, false);
        }

        $editor2 = Oledrion\Utility::getWysiwygForm(_MI_OLEDRION_ADVERTISEMENT, 'cat_advertisement', $item->getVar('cat_advertisement', 'e'), 15, 60, 'pub_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        // META Data
        if ($manual_meta) {
            $sform->addElement(new \XoopsFormText(_AM_OLEDRION_META_KEYWORDS, 'cat_metakeywords', 50, 255, $item->getVar('cat_metakeywords', 'e')), false);
            $sform->addElement(new \XoopsFormText(_AM_OLEDRION_META_DESCRIPTION, 'cat_metadescription', 50, 255, $item->getVar('cat_metadescription', 'e')), false);
            $sform->addElement(new \XoopsFormText(_AM_OLEDRION_META_PAGETITLE, 'cat_metatitle', 50, 255, $item->getVar('cat_metatitle', 'e')), false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();
        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'une catégorie

        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('cat_cid', 0, 'POST');
        if (!empty($id)) {
            $edit = true;
            $item = $categoryHandler->get($id);
            if (!is_object($item)) {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $add = false;
        } else {
            $item = $categoryHandler->create(true);
            $add  = true;
        }
        $opRedirect = 'categories';
        $item->setVars($_POST);

        if (\Xmf\Request::hasVar('delpicture', 'POST') && 1 == \Xmf\Request::getInt('delpicture', 0, 'POST')) {
            $item->deletePicture();
        }

        $destname = '';
        $res1     = Oledrion\Utility::uploadFile(0, OLEDRION_PICTURES_PATH);
        if (true === $res1) {
            if (Oledrion\Utility::getModuleOption('resize_others')) {
                // Eventuellement on redimensionne l'image
                Oledrion\Utility::resizePicture(OLEDRION_PICTURES_PATH . '/' . $destname, OLEDRION_PICTURES_PATH . '/' . $destname, Oledrion\Utility::getModuleOption('images_width'), Oledrion\Utility::getModuleOption('images_height'), true);
            }
            $item->setVar('cat_imgurl', basename($destname));
        } else {
            if (false !== $res1) {
                echo $res1;
            }
        }

        $res = $categoryHandler->insert($item);
        if ($res) {
            Oledrion\Utility::updateCache();
            if ($add) {
                //$plugins = Plugin::getInstance();
                //$plugins->fireAction(Plugin::EVENT_ON_CATEGORY_CREATE, new Oledrion\Parameters(array('category' => $item)));
            }
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }

        break;
    // ****************************************************************************************************************
    case 'delete': // Suppression d'une catégorie

        // ****************************************************************************************************************
        xoops_cp_header();
        oledrion_adminMenu(3);
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (0 == $id) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $category = null;
        $category = $categoryHandler->get($id);
        if (!is_object($category)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_10, $baseurl, 5);
        }
        $msg = sprintf(_AM_OLEDRION_CONF_DEL_CATEG, $category->getVar('cat_title'));
        xoops_confirm(['op' => 'categories', 'action' => 'confdelete', 'id' => $id], 'index.php', $msg);

        break;
    // ****************************************************************************************************************
    case 'confdelete': //Suppression effective d'une catégorie

        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (empty($id)) {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'categories';
        // On vérifie que cette catégorie (et ses sous-catégories) ne sont pas utilisées par des produits
        $cnt = $categoryHandler->getCategoryProductsCount($id);
        if (0 == $cnt) {
            $item = null;
            $item = $categoryHandler->get($id);
            if (is_object($item)) {
                $res = $categoryHandler->deleteCategory($item);
                if ($res) {
                    Oledrion\Utility::updateCache();
                    Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                    Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
                Oledrion\Utility::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            Oledrion\Utility::redirect(_AM_OLEDRION_ERROR_4, $baseurl . '?op=' . $opRedirect, 5);
        }

        break;
}
