<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

require_once __DIR__ . '/../../../mainfile.php';

$moduleDirName = basename(dirname(__DIR__));
$capsDirName   = strtoupper($moduleDirName);

if (!defined($capsDirName . '_DIRNAME')) {
    //if (!defined(constant($capsDirName . '_DIRNAME'))) {
    define($capsDirName . '_DIRNAME', $GLOBALS['xoopsModule']->dirname());
    define($capsDirName . '_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($capsDirName . '_DIRNAME'));
    define($capsDirName . '_URL', XOOPS_URL . '/modules/' . constant($capsDirName . '_DIRNAME'));
    define($capsDirName . '_ADMIN', constant($capsDirName . '_URL') . '/admin/index.php');
    define($capsDirName . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($capsDirName . '_DIRNAME'));
    define($capsDirName . '_AUTHOR_LOGOIMG', constant($capsDirName . '_URL') . '/assets/images/logoModule.png');
}

// Define here the place where main upload path

//$img_dir = $GLOBALS['xoopsModuleConfig']['uploaddir'];

define($capsDirName . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
//define("XXXXXX_UPLOAD_PATH", $img_dir); // WITHOUT Trailing slash
define($capsDirName . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash

//constant($cloned_lang . '_CATEGORY_NOTIFY')

$uploadFolders = [
    constant($capsDirName . '_UPLOAD_PATH'),
    constant($capsDirName . '_UPLOAD_PATH') . '/images',
    constant($capsDirName . '_UPLOAD_PATH') . '/images/thumbnails'
];

$copyFiles = [
    constant($capsDirName . '_UPLOAD_PATH'),
    constant($capsDirName . '_UPLOAD_PATH') . '/images',
    constant($capsDirName . '_UPLOAD_PATH') . '/images/thumbnails'
];

$oldFiles = [
    '/include/update_functions.php',
    '/include/install_functions.php'
];

//Configurator
return (object)[
    'name'          => 'Module Configurator',
    'uploadFolders' => [
        constant($capsDirName . '_UPLOAD_PATH'),
        constant($capsDirName . '_UPLOAD_PATH') . '/XXX'
    ],
    'blankFiles'     => [
        constant($capsDirName . '_UPLOAD_PATH'),
        constant($capsDirName . '_UPLOAD_PATH') . '/XXXX'
    ],

    'templateFolders' => [
        '/templates/',
        '/templates/blocks/',
        '/templates/admin/'

    ],
    'oldFiles'        => [
        '/include/update_functions.php',
        '/include/install_functions.php'
    ],
    'oldFolders'        => [
        '/images',
        '/css',
        '/js',
        '/pdf',
        '/images',
    ],
];

