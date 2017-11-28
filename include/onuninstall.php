<?php
/**
 * uninstall.php - cleanup on module uninstall
 *
 * @author          XOOPS Module Development Team
 * @copyright       {@link https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @link            https://xoops.org XOOPS
 */

/**
 * Prepares system prior to attempting to uninstall module
 * @param XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if ready to uninstall, false if not
 */

function xoops_module_pre_uninstall_xxxx(XoopsModule $module)
{
    // Do some synchronization
    return true;
}

/**
 *
 * Performs tasks required during uninstallation of the module
 * @param XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if uninstallation successful, false if not
 */
function xoops_module_uninstall_xxxx(XoopsModule $module)
{
    return true;
}

//=======================================================

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function xoops_module_uninstall_XXXX(XoopsModule $module)
{
    // global $xoopsDB,$xoopsConfig;
    //
    // nothing to do yet
    return true;

    //routine to delete a cache directory
    /*
     $cacheDir = XOOPS_ROOT_PATH . '/uploads/shoutbox';
    //Always check if a directory exists prior to creation
    if (!is_dir($cacheDir)) {
        return true;
    } else {
        return rmdirr($cacheDir); // see the function below
    }
     */

    //------------- example from user log --------------
    /*
     $logsetObj = UserlogSetting::getInstance();

    return $logsetObj->cleanCache(); // delete all settings caches

     */
}

/**
 * Delete a file, or a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @param  string $dirname The directory to delete
 * @return bool   Returns true on success, false on failure
 */

function rmdirr($dirname)
{
    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ('.' === $entry || '..' === $entry) {
            continue;
        }

        // Deep delete directories
        if (is_dir("$dirname/$entry")) {
            rmdirr("$dirname/$entry");
        } else {
            unlink("$dirname/$entry");
        }
    }

    // Clean up
    $dir->close();

    return rmdir($dirname);
}
