<?php namespace XoopsModules\Oledrion;

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
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/classheader.php';

/**
 * Class Location
 */
class Location extends OledrionObject
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
    public function __construct()
    {
        $this->initVar('location_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('location_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('location_type', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = [];
        $ret = parent::toArray($format);

        return $ret;
    }
}
