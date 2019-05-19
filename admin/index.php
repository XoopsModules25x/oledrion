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

use XoopsModules\Oledrion;

require_once __DIR__ . '/admin_header.php';

require_once OLEDRION_PATH . 'admin/functions.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
require_once XOOPS_ROOT_PATH . '/class/uploader.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
//require_once OLEDRION_PATH . 'class/tree.php';

// Reading some parameters of the application ********************************************************************
$limit            = Oledrion\Utility::getModuleOption('items_count'); // Maximum number of items to display in the admin
$baseurl          = OLEDRION_URL . 'admin/' . basename(__FILE__); // URL de ce script
$conf_msg         = Oledrion\Utility::javascriptLinkConfirm(_AM_OLEDRION_CONF_DELITEM);
$oledrionCurrency = Oledrion\Currency::getInstance();
$manual_meta      = Oledrion\Utility::getModuleOption('manual_meta');

$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

// Verifying the Existence of the Cache Directory
Oledrion\Utility::prepareFolder(OLEDRION_UPLOAD_PATH);
Oledrion\Utility::prepareFolder(OLEDRION_ATTACHED_FILES_PATH);
Oledrion\Utility::prepareFolder(OLEDRION_PICTURES_PATH);
Oledrion\Utility::prepareFolder(OLEDRION_CSV_PATH);
Oledrion\Utility::prepareFolder(OLEDRION_CACHE_PATH);
Oledrion\Utility::prepareFolder(OLEDRION_TEXT_PATH);

// Is the cache directory open for writing? ?
if (!is_writable(OLEDRION_CACHE_PATH)) {
    exit('Your cache folder, ' . OLEDRION_CACHE_PATH . ' is not writable !');
}

// ************************************************************************************************
$destname = '';

if (!defined('OLEDRION_ADMIN')) {
    define('OLEDRION_ADMIN', true);
}

$op     = \Xmf\Request::getCmd('op', 'dashboard');
$action = \Xmf\Request::getCmd('action', 'default');

// Check admin have access to this page
$part = Oledrion\Utility::getModuleOption('admin_groups_part');
$part = explode('|', $part);
if (!in_array($op, $part)) {
    $group  = $xoopsUser->getGroups();
    $groups = Oledrion\Utility::getModuleOption('admin_groups');
    if (count(array_intersect($group, $groups)) <= 0) {
        redirect_header('index.php', 3, _NOPERM);
    }
}

$op        = str_replace('..', '', $op);
$controler = OLEDRION_ADMIN_PATH . 'actions/' . $op . '.php';
if (file_exists($controler)) {
    require_once $controler;
}

//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
