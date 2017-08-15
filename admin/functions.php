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
 * @param int    $currentoption
 * @param string $breadcrumb
 */
function oledrion_adminMenu($currentoption = 0, $breadcrumb = '')
{
}

/**
 * Internal function
 */
function oledrion_get_mid()
{
    global $xoopsModule;

    return $xoopsModule->getVar('mid');
}

/**
 * Internal function
 */
function oledrion_get_configHandler()
{
    $configHandler = null;
    $configHandler = xoops_getHandler('config');
    if (!is_object($configHandler)) {
        trigger_error('Error, unable to get and handler on the Config object');
        exit;
    } else {
        return $configHandler;
    }
}

/**
 * Returns a module option
 *
 * @param  string $optionName The module's option
 * @return \XoopsConfigOption The requested module's option
 */
function oledrion_get_module_option($optionName = '')
{
    $ret           = null;
    $tbl_options   = [];
    $mid           = oledrion_get_mid();
    $configHandler = oledrion_get_configHandler();
    $critere       = new CriteriaCompo();
    $critere->add(new Criteria('conf_modid', $mid, '='));
    $critere->add(new Criteria('conf_name', $optionName, '='));
    $tbl_options = $configHandler->getConfigs($critere, false, false);
    if (count($tbl_options) > 0) {
        $option = $tbl_options[0];
        $ret    = $option;
    }

    return $ret;
}

/**
 * Set a module's option
 * @param string $optionName
 * @param string $optionValue
 * @return
 */
function oledrion_set_module_option($optionName = '', $optionValue = '')
{
    $configHandler = oledrion_get_configHandler();
    $option        = oledrion_get_module_option($optionName, true);
    $option->setVar('conf_value', $optionValue);
    $retval = $configHandler->insertConfig($option, true);

    return $retval;
}

/**
 * Affichage du pied de page de l'administration
 *
 * @return string La chaine à afficher
 */
function show_footer()
{
    //  echo "<br><br><div align='center'><a href='http://www.herve-thouzard.com/' target='_blank'><img src='../assets/images/instantzero.gif'></a></div>";
}
