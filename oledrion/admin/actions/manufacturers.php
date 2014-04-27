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
 * @version     $Id: manufacturers.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Gestion des fabricants (dans l'administration)
 */
if (!defined("OLEDRION_ADMIN")) exit();
switch ($action) {
    // ****************************************************************************************************************
    case 'default': // Liste des fabricants
        // ****************************************************************************************************************
        xoops_cp_header();
        $vats = array();
        $form = "<form method='post' action='$baseurl' name='frmaddmanufacturer' id='frmaddmanufacturer'><input type='hidden' name='op' id='op' value='manufacturers' /><input type='hidden' name='action' id='action' value='add' /><input type='submit' name='btngo' id='btngo' value='" . _AM_OLEDRION_ADD_ITEM . "' /></form>";
        echo $form;
        oledrion_utils::htitle(_MI_OLEDRION_ADMENU3, 4);

        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('manu_id', 0, '<>'));

        $itemsCount = $h_oledrion_manufacturer->getCount($criteria); // Recherche du nombre total de fabricants
        if ($itemsCount > $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=manufacturers');
        }

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('manu_name, manu_commercialname');

        $manufacturers = $h_oledrion_manufacturer->getObjects($criteria);
        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<tr><td colspan='2' align='left'>" . $pagenav->renderNav() . "</td><td align='right' colspan='3'>&nbsp;</td></tr>\n";
        }
        echo "<tr><th align='center'>" . _OLEDRION_LASTNAME . "</th><th align='center'>" . _OLEDRION_COMM_NAME . "</th><th align='center'>" . _OLEDRION_EMAIL . "</th><th align='center'>" . _AM_OLEDRION_ACTION . "</th></tr>";
        foreach ($manufacturers as $item) {
            $class = ($class == 'even') ? 'odd' : 'even';
            $id = $item->getVar('manu_id');
            $actions = array();
            $actions[] = "<a href='$baseurl?op=manufacturers&action=edit&id=" . $id . "' title='" . _OLEDRION_EDIT . "'>" . $icones['edit'] . '</a>';
            $actions[] = "<a href='$baseurl?op=manufacturers&action=delete&id=" . $id . "' title='" . _OLEDRION_DELETE . "'" . $conf_msg . ">" . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td><a href='" . $item->getLink() . "'>" . $item->getVar('manu_name') . "</a></td><td align='left'>" . $item->getVar('manu_commercialname') . "</td><td align='center'>" . $item->getVar('manu_email') . "</td><td align='center'>" . implode(' ', $actions) . "</td>\n";
            echo "<tr>\n";
        }
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        if (isset($pagenav) && is_object($pagenav)) {
            echo "<div align='right'>" . $pagenav->renderNav() . "</div>";
        }
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;


    // ****************************************************************************************************************
    case 'add': // Ajout d'un fabricant
    case 'edit': // Edition d'un fabricant
        // ****************************************************************************************************************
        xoops_cp_header();
        if ($action == 'edit') {
            $title = _AM_OLEDRION_EDIT_MANUFACTURER;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (empty($id)) {
                oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $h_oledrion_manufacturer->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $edit = true;
            $label_submit = _AM_OLEDRION_MODIFY;
        } else {
            $title = _AM_OLEDRION_ADD_MANUFACTURER;
            $item = $h_oledrion_manufacturer->create(true);
            $label_submit = _AM_OLEDRION_ADD;
            $edit = false;
        }

        $sform = new XoopsThemeForm($title, 'frmmanufacturer', $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new XoopsFormHidden('op', 'manufacturers'));
        $sform->addElement(new XoopsFormHidden('action', 'saveedit'));
        $sform->addElement(new XoopsFormHidden('manu_id', $item->getVar('manu_id')));
        $sform->addElement(new XoopsFormText(_OLEDRION_LASTNAME, 'manu_name', 50, 255, $item->getVar('manu_name', 'e')), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_COMM_NAME, 'manu_commercialname', 50, 255, $item->getVar('manu_commercialname', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_EMAIL, 'manu_email', 50, 255, $item->getVar('manu_email', 'e')), false);
        $sform->addElement(new XoopsFormText(_OLEDRION_SITEURL, 'manu_url', 50, 255, $item->getVar('manu_url', 'e')), false);

        $editor = oledrion_utils::getWysiwygForm(_OLEDRION_MANUFACTURER_INF, 'manu_bio', $item->getVar('manu_bio', 'e'), 15, 60, 'bio_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }
        // Les 5 images
        for ($i = 1; $i <= 5; $i++) {
            if ($action == 'edit' && $item->pictureExists($i)) {
                $pictureTray = new XoopsFormElementTray(_AM_OLEDRION_CURRENT_PICTURE, '<br />');
                $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . $item->getPictureUrl($i) . "' alt='' border='0' />"));
                $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture' . $i);
                $deleteCheckbox->addOption(1, _DELETE);
                $pictureTray->addElement($deleteCheckbox);
                $sform->addElement($pictureTray);
                unset($pictureTray, $deleteCheckbox);
            }
            $sform->addElement(new XoopsFormFile(_AM_OLEDRION_PICTURE . ' ' . $i, 'attachedfile' . $i, oledrion_utils::getModuleOption('maxuploadsize')), false);
        }

        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform = oledrion_utils::formMarkRequiredFields($sform);
        $sform->display();
        include_once OLEDRION_ADMIN_PATH . 'admin_footer.php';
        break;

    // ****************************************************************************************************************
    case 'saveedit': // Sauvegarde d'un fabricant
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_POST['manu_id']) ? intval($_POST['manu_id']) : 0;
        if (!empty($id)) {
            $edit = true;
            $item = $h_oledrion_manufacturer->get($id);
            if (!is_object($item)) {
                oledrion_utils::redirect(_AM_OLEDRION_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $h_oledrion_manufacturer->create(true);
        }
        $opRedirect = 'manufacturers';
        $item->setVars($_POST);
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST['delpicture' . $i]) && intval($_POST['delpicture' . $i]) == 1) {
                $item->deletePicture($i);
            }
        }

        // Upload des fichiers
        for ($i = 1; $i <= 5; $i++) {
            $res1 = oledrion_utils::uploadFile($i - 1, OLEDRION_PICTURES_PATH);
            if ($res1 === true) {
                if (oledrion_utils::getModuleOption('resize_others')) { // Eventuellement on redimensionne l'image
                    oledrion_utils::resizePicture(OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, OLEDRION_PICTURES_PATH . DIRECTORY_SEPARATOR . $destname, oledrion_utils::getModuleOption('images_width'), oledrion_utils::getModuleOption('images_height'), true);
                }
                $item->setVar('manu_photo' . $i, basename($destname));
            } else {
                if ($res1 !== false) {
                    echo $res1;
                }
            }
        }

        $res = $h_oledrion_manufacturer->insert($item);
        if ($res) {
            oledrion_utils::updateCache();
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
            oledrion_utils::redirect(_AM_OLEDRION_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;


    // ****************************************************************************************************************
    case 'delete': // Suppression d'un fabricant
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($id)) {
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'manufacturers';
        // On vérifie que ce fabriquant n'est pas relié à des produits
        $cnt = $h_oledrion_manufacturer->getManufacturerProductsCount($id);
        if ($cnt == 0) {
            $item = null;
            $item = $h_oledrion_manufacturer->get($id);
            if (is_object($item)) {
                $res = $h_oledrion_manufacturer->deleteManufacturer($item);
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
            oledrion_utils::redirect(_AM_OLEDRION_ERROR_5, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;
}
