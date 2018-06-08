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
 * Panier persistant
 *
 * Lorque l'option correspondante dans le module est activée, tout produit rajouté dans le panier est
 * enregistré en base de données (à condition que l'utilisateur soit connecté).
 * Si l'utilisateur quitte le site et revient plus tard, cela permet de recharger son panier.
 */
// require_once __DIR__ . '/classheader.php';

/**
 * Class PersistentCart
 */
class PersistentCart extends OledrionObject
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
        $this->initVar('persistent_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('persistent_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('persistent_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('persistent_date', XOBJ_DTYPE_INT, null, false);
        $this->initVar('persistent_qty', XOBJ_DTYPE_INT, null, false);
    }
}
