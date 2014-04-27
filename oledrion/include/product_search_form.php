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
 * @version     $Id: product_search_form.php 12290 2014-02-07 11:05:17Z beckmi $
 */

/**
 * Recherche avancée dans les produits, formulaire de sélection des critères
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once OLEDRION_PATH . 'class/tree.php';

$sform = new XoopsThemeForm(oledrion_utils::getModuleName() . ' - ' . _OLEDRION_SEARCHFOR, 'productsSearchForm', OLEDRION_URL . 'search.php', 'post');
$sform->addElement(new XoopsFormText(_OLEDRION_TEXT, 'product_text', 50, 255, ''), false);
$sform->addElement(new XoopsFormSelectMatchOption(_OLEDRION_TYPE, 'search_type', 3), false);

// Sélecteur de catégories ****************************************************
if (oledrion_utils::getModuleOption('search_category')) {
    $categorySelect = new XoopsFormSelect(_OLEDRION_CATEGORY, 'product_category', 0);
    $treeObject = new Oledrion_XoopsObjectTree($categories, 'cat_cid', 'cat_pid');
    $tree = $treeObject->makeTreeAsArray('cat_title', '-', 0, _OLEDRION_ALL_CATEGORIES);
    $categorySelect->addOptionArray($tree);
    $sform->addElement($categorySelect, false);
}

// Sélecteur pour les fabricants *************************************************
if (oledrion_utils::getModuleOption('search_manufacturers')) {
    $authorSelect = new XoopsFormSelect(_OLEDRION_MANUFACTURER, 'product_manufacturers', 0, 5, true);
    $tblTmp = array();
    $tblTmp[0] = _OLEDRION_ALL_MANUFACTURERS;
    foreach ($manufacturers as $item) {
        $tblTmp[$item->getVar('manu_id')] = $item->getVar('manu_commercialname') . ' ' . $item->getVar('manu_name');
    }
    $authorSelect->addOptionArray($tblTmp);
    $sform->addElement($authorSelect, false);
}

// Sélecteur pour les vendeurs *************************************************
if (oledrion_utils::getModuleOption('search_vendors')) {
    $languageSelect = new XoopsFormSelect(_OLEDRION_VENDOR, 'product_vendors', 0, 1, false);
    $tblTmp = array();
    $tblTmp[0] = _OLEDRION_ALL_VENDORS;
    foreach ($vendors as $item) {
        $tblTmp[$item->getVar('vendor_id')] = $item->getVar('vendor_name');
    }
    $languageSelect->addOptionArray($tblTmp);
    $sform->addElement($languageSelect, false);
}

//
if (oledrion_utils::getModuleOption('search_price')) {
    $sform->addElement(new XoopsFormText(_OLEDRION_FROM, 'product_from', 10, 10, ''), false);
    $sform->addElement(new XoopsFormText(_OLEDRION_TO, 'product_to', 10, 10, ''), false);
}

//
if (oledrion_utils::getModuleOption('search_stocks')) {
    $stockselect = new XoopsFormSelect(_OLEDRION_QUANTITYS, 'product_stock', 1);
    $stockselect->addOption(1, _OLEDRION_QUANTITYALL);
    $stockselect->addOption(2, _OLEDRION_QUANTITY1);
    $stockselect->addOption(0, _OLEDRION_QUANTITY2);
    $sform->addElement($stockselect, false);
}

//
if (oledrion_utils::getModuleOption('search_property1') && oledrion_utils::getModuleOption('product_property1')) {
    $property1select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property1_title'), 'product_property1', '');
    $property1Array = explode('|', oledrion_utils::getModuleOption('product_property1'));
    foreach ($property1Array as $property1) {
        $property1select->addOption($property1);
    }
    $sform->addElement($property1select, false);
}

//
if (oledrion_utils::getModuleOption('search_property2') && oledrion_utils::getModuleOption('product_property2')) {
    $property2select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property2_title'), 'product_property2', '');
    $property2Array = explode('|', oledrion_utils::getModuleOption('product_property2'));
    foreach ($property2Array as $property2) {
        $property2select->addOption($property2);
    }
    $sform->addElement($property2select, false);
}

//
if (oledrion_utils::getModuleOption('search_property3') && oledrion_utils::getModuleOption('product_property3')) {
    $property3select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property3_title'), 'product_property3', '');
    $property3Array = explode('|', oledrion_utils::getModuleOption('product_property3'));
    foreach ($property3Array as $property3) {
        $property3select->addOption($property3);
    }
    $sform->addElement($property3select, false);
}

//
if (oledrion_utils::getModuleOption('search_property4') && oledrion_utils::getModuleOption('product_property4')) {
    $property4select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property4_title'), 'product_property4', '');
    $property4Array = explode('|', oledrion_utils::getModuleOption('product_property4'));
    foreach ($property4Array as $property4) {
        $property4select->addOption($property4);
    }
    $sform->addElement($property4select, false);
}

//
if (oledrion_utils::getModuleOption('search_property5') && oledrion_utils::getModuleOption('product_property5')) {
    $property5select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property5_title'), 'product_property5', '');
    $property5Array = explode('|', oledrion_utils::getModuleOption('product_property5'));
    foreach ($property5Array as $property5) {
        $property5select->addOption($property5);
    }
    $sform->addElement($property5select, false);
}

//
if (oledrion_utils::getModuleOption('search_property6') && oledrion_utils::getModuleOption('product_property6')) {
    $property6select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property6_title'), 'product_property6', '');
    $property6Array = explode('|', oledrion_utils::getModuleOption('product_property6'));
    foreach ($property6Array as $property6) {
        $property6select->addOption($property6);
    }
    $sform->addElement($property6select, false);
}

//
if (oledrion_utils::getModuleOption('search_property7') && oledrion_utils::getModuleOption('product_property7')) {
    $property7select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property7_title'), 'product_property7', '');
    $property7Array = explode('|', oledrion_utils::getModuleOption('product_property7'));
    foreach ($property7Array as $property7) {
        $property7select->addOption($property7);
    }
    $sform->addElement($property7select, false);
}

//
if (oledrion_utils::getModuleOption('search_property8') && oledrion_utils::getModuleOption('product_property8')) {
    $property8select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property8_title'), 'product_property8', '');
    $property8Array = explode('|', oledrion_utils::getModuleOption('product_property8'));
    foreach ($property8Array as $property8) {
        $property8select->addOption($property8);
    }
    $sform->addElement($property8select, false);
}

//
if (oledrion_utils::getModuleOption('search_property9') && oledrion_utils::getModuleOption('product_property9')) {
    $property9select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property9_title'), 'product_property9', '');
    $property9Array = explode('|', oledrion_utils::getModuleOption('product_property9'));
    foreach ($property9Array as $property9) {
        $property9select->addOption($property9);
    }
    $sform->addElement($property9select, false);
}

//
if (oledrion_utils::getModuleOption('search_property10') && oledrion_utils::getModuleOption('product_property10')) {
    $property10select = new XoopsFormSelect(oledrion_utils::getModuleOption('product_property10_title'), 'product_property10', '');
    $property10Array = explode('|', oledrion_utils::getModuleOption('product_property10'));
    foreach ($property10Array as $property10) {
        $property10select->addOption($property10);
    }
    $sform->addElement($property10select, false);
}

$sform->addElement(new XoopsFormHidden('op', 'go'));

$button_tray = new XoopsFormElementTray('', '');
$submit_btn = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);
