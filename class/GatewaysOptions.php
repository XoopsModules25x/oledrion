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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

use XoopsModules\Oledrion;

/**
 * Gestion des options des passerelles de paiement
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class GatewaysOptions
 */
class GatewaysOptions extends OledrionObject
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
        $this->initVar('option_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('option_gateway', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('option_name', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('option_value', XOBJ_DTYPE_TXTAREA, null, false);
    }
}
