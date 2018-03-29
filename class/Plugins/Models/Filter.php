<?php namespace XoopsModules\Oledrion\plugins\models;
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
 * Every filter plugin must extend this class
 */
// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

abstract class Filter
{
    /**
     * Retourne la liste des évènements traités par le plugin
     * @return array
     */
    abstract public static function registerEvents();
}
