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
 * Affichage des conditions générales de vente
 */
require __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_cgv.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once OLEDRION_PATH . 'class/registryfile.php';

$registry = new oledrion_registryfile();

$xoopsTpl->assign('nostock_msg', Oledrion_utils::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('cgv_msg', $registry->getfile(OLEDRION_TEXTFILE2));

$xoopsTpl->assign('breadcrumb', Oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_CGV)));

Oledrion_utils::setCSS();
Oledrion_utils::setLocalCSS($xoopsConfig['language']);
Oledrion_utils::setMetas(_OLEDRION_CGV . ' ' . Oledrion_utils::getModuleName(), _OLEDRION_CGV . ' ' . Oledrion_utils::getModuleName());
require_once XOOPS_ROOT_PATH . '/footer.php';
