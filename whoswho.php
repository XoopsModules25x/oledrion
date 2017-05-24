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
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Liste des fabricants
 */
require __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_whoswho.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$tblAll = $tblAnnuaire = array();
$xoopsTpl->assign('alphabet', $h_oledrion_manufacturer->getAlphabet());
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module

$manufacturers = $h_oledrion_manufacturer->getItems(0, 0, 'manu_name, manu_commercialname');
foreach ($manufacturers as $item) {
    $forTemplate              = array();
    $forTemplate              = $item->toArray();
    $initiale                 = $item->getInitial();
    $tblAnnuaire[$initiale][] = $forTemplate;
}
$xoopsTpl->assign('manufacturers', $tblAnnuaire);

Oledrion_utils::setCSS();
Oledrion_utils::setLocalCSS($xoopsConfig['language']);
Oledrion_utils::loadLanguageFile('modinfo.php');

$xoopsTpl->assign('global_advert', Oledrion_utils::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', Oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME5)));

$title = _MI_OLEDRION_SMNAME5 . ' - ' . Oledrion_utils::getModuleName();
Oledrion_utils::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
