<?php namespace Xoopsmodules\oledrion;

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

use Xoopsmodules\oledrion;

/**
 * Gestion des TVA
 */
require_once __DIR__ . '/classheader.php';

/**
 * Class Vat
 */
class Vat extends OledrionObject
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
        $this->initVar('vat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('vat_rate', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('vat_country', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * @param  string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                      = [];
        $ret                      = parent::toArray($format);
        $oledrion_Currency        = oledrion\Currency::getInstance();
        $ret['vat_rate_formated'] = $oledrion_Currency->amountInCurrency((float)$this->getVar('vat_rate', 'e'));

        return $ret;
    }
}
