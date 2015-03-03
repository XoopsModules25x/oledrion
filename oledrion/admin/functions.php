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
 * @version     $Id: functions.php 12290 2014-02-07 11:05:17Z beckmi $
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
function oledrion_get_config_handler()
{
    $config_handler = null;
    $config_handler = xoops_gethandler('config');
    if (!is_object($config_handler)) {
        trigger_error("Error, unable to get and handler on the Config object");
        exit;
    } else {
        return $config_handler;
    }

}

/**
 * Returns a module option
 *
 * @param string    $option_name    The module's option
 * @return object    The requested module's option
 */
function oledrion_get_module_option($optionName = '')
{
    $ret = null;
    $tbl_options = array();
    $mid = oledrion_get_mid();
    $config_handler = oledrion_get_config_handler();
    $critere = new CriteriaCompo();
    $critere->add(new Criteria('conf_modid', $mid, '='));
    $critere->add(new Criteria('conf_name', $optionName, '='));
    $tbl_options = $config_handler->getConfigs($critere, false, false);
    if (count($tbl_options) > 0) {
        $option = $tbl_options[0];
        $ret = $option;
    }

    return $ret;
}

/**
 * Set a module's option
 */
function oledrion_set_module_option($optionName = '', $optionValue = '')
{
    $config_handler = oledrion_get_config_handler();
    $option = oledrion_get_module_option($optionName, true);
    $option->setVar('conf_value', $optionValue);
    $retval = $config_handler->insertConfig($option, true);

    return $retval;
}

/**
 * Affichage du pied de page de l'administration
 *
 * @return string    La chaine à afficher
 */
function show_footer()
{
//	echo "<br /><br /><div align='center'><a href='http://www.herve-thouzard.com/' target='_blank'><img src='../assets/images/instantzero.gif'></a></div>";
}
