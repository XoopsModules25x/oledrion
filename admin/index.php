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

require_once __DIR__ . '/../../../include/cp_header.php';
require_once __DIR__ . '/../include/common.php';
require_once __DIR__ . '/admin_header.php';

require_once OLEDRION_PATH . 'admin/functions.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
require_once XOOPS_ROOT_PATH . '/class/uploader.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once OLEDRION_PATH . 'class/tree.php';

// Lecture de certains param�tres de l'application ********************************************************************
$limit             = OledrionUtility::getModuleOption('items_count'); // Nombre maximum d'�l�ments � afficher dans l'admin
$baseurl           = OLEDRION_URL . 'admin/' . basename(__FILE__); // URL de ce script
$conf_msg          = OledrionUtility::javascriptLinkConfirm(_AM_OLEDRION_CONF_DELITEM);
$oledrion_Currency = Oledrion_Currency::getInstance();
$manual_meta       = OledrionUtility::getModuleOption('manual_meta');

OledrionUtility::loadLanguageFile('modinfo.php');
OledrionUtility::loadLanguageFile('main.php');

// V�rification de l'existance du r�pertoire de cache
OledrionUtility::prepareFolder(OLEDRION_UPLOAD_PATH);
OledrionUtility::prepareFolder(OLEDRION_ATTACHED_FILES_PATH);
OledrionUtility::prepareFolder(OLEDRION_PICTURES_PATH);
OledrionUtility::prepareFolder(OLEDRION_CSV_PATH);
OledrionUtility::prepareFolder(OLEDRION_CACHE_PATH);
OledrionUtility::prepareFolder(OLEDRION_TEXT_PATH);

// Est-ce que le r�pertoire du cache est ouvert en �criture ?
if (!is_writable(OLEDRION_CACHE_PATH)) {
    exit('Your cache folder, ' . OLEDRION_CACHE_PATH . ' is not writable !');
}

// ************************************************************************************************
$destname = '';
define('OLEDRION_ADMIN', true);

$op = 'dashboard';
if (isset($_POST['op'])) {
    $op = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
} elseif (isset($_GET['op'])) {
    $op = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);
}

$action = 'default';
if (isset($_POST['action'])) {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
} elseif (isset($_GET['action'])) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
}

// Check admin have access to this page
$part = OledrionUtility::getModuleOption('admin_groups_part');
$part = explode('|', $part);
if (!in_array($op, $part)) {
    $group  = $xoopsUser->getGroups();
    $groups = OledrionUtility::getModuleOption('admin_groups');
    if (count(array_intersect($group, $groups)) <= 0) {
        redirect_header('index.php', 3, _NOPERM);
    }
}

$op        = str_replace('..', '', $op);
$controler = OLEDRION_ADMIN_PATH . 'actions/' . $op . '.php';
if (file_exists($controler)) {
    require $controler;
}

//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
