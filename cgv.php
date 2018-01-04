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

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_cgv.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
//require_once OLEDRION_PATH . 'class/Registryfile.php';

$registry = new Oledrion\Registryfile();

$xoopsTpl->assign('nostock_msg', Oledrion\Utility::getModuleOption('nostock_msg'));
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module
$xoopsTpl->assign('cgv_msg', $registry->getfile(OLEDRION_TEXTFILE2));

$xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_CGV]));

Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
Oledrion\Utility::setMetas(_OLEDRION_CGV . ' ' . Oledrion\Utility::getModuleName(), _OLEDRION_CGV . ' ' . Oledrion\Utility::getModuleName());
require_once XOOPS_ROOT_PATH . '/footer.php';
