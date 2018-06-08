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
 * Gestion des caddy
 */
// require_once __DIR__ . '/classheader.php';

/**
 * Class Caddy
 */
class Caddy extends OledrionObject
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
        $this->initVar('caddy_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_qte', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_price', XOBJ_DTYPE_TXTBOX, null, false); // Prix TTC
        $this->initVar('caddy_cmd_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_shipping', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('caddy_pass', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Retourne les éléments du produits formatés pour affichage
     *
     * @param  string $format Le format à utiliser
     * @return array  Les informations formatées
     */
    public function toArray($format = 's')
    {
        $ret                              = [];
        $ret                              = parent::toArray($format);
        $oledrion_Currency                = Oledrion\Currency::getInstance();
        $ret['caddy_price_fordisplay']    = $oledrion_Currency->amountForDisplay($this->getVar('caddy_price'));
        $ret['caddy_shipping_fordisplay'] = $oledrion_Currency->amountForDisplay($this->getVar('caddy_shipping'));

        return $ret;
    }
}
