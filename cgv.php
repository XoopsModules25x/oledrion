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
 * Affichage des conditions générales de vente
 */
require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_cgv.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once OLEDRION_PATH . 'class/registryfile.php';

$registry = new oledrion_registryfile();

$xoopsTpl->assign('nostock_msg', OledrionUtility::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('cgv_msg', $registry->getfile(OLEDRION_TEXTFILE2));

$xoopsTpl->assign('breadcrumb', OledrionUtility::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_CGV)));

OledrionUtility::setCSS();
OledrionUtility::setLocalCSS($xoopsConfig['language']);
OledrionUtility::setMetas(_OLEDRION_CGV . ' ' . OledrionUtility::getModuleName(), _OLEDRION_CGV . ' ' . OledrionUtility::getModuleName());
require_once XOOPS_ROOT_PATH . '/footer.php';
